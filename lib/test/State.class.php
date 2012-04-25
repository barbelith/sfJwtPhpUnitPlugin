<?php
/**
 * Copyright (c) 2011 J. Walter Thompson dba JWT
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/** Maintains global state for test cases.
 *
 * @author Phoenix Zerin <phoenix@todofixthis.com>
 *
 * @package sfJwtPhpUnitPlugin
 * @subpackage lib.test
 */
class Test_State
{
  static private
    $_dbRebuilt,
    $_dbFlushTree,
    $_configs;

  /** @var sfProjectConfiguration */
  protected $_configuration;
  /** @var Doctrine_Connection */
  protected $_connection;

  /** Inits the class instance.
   *
   * @param $configuration sfProjectConfiguration
   */
  public function __construct( sfProjectConfiguration $configuration )
  {
    $this->_configuration = $configuration;

    $this->_connection    = $this->getDatabaseConnection();
  }

  /** Flush the database and reload base fixtures.
   *
   * @param bool $rebuild
   *  true:   The database will be dropped and rebuilt.
   *  false:  The method will try just to flush the data.
   *
   * Note that the first time flushDatabase() is called (per execution), the
   *  database will be rebuilt regardless of $rebuild.
   *
   * @return static
   *
   * @todo Move Doctrine-specific functionality into separate class.
   */
  public function flushDatabase( $rebuild = false )
  {
    if( $this->_connection )
    {
      if (!sfConfig::get('app_sfJwtPhpUnitPlugin_rebuild_database_on_first_test'))
      {
        self::$_dbRebuilt = true;
      }

      /* The first time we run a test case, drop and rebuild the database.
       *
       * After that, we can simply truncate all tables for speed.
       */
      if( empty(self::$_dbRebuilt) or $rebuild )
      {
        /* Don't try to drop the database unless it exists. */
        $name = $this->getDatabaseName();
        /** @noinspection PhpUndefinedFieldInspection */
        if( $name and $this->_connection->import->databaseExists($name) )
        {
          $this->_connection->dropDatabase();
        }

        $this->_connection->createDatabase();

        Doctrine_Core::loadModels(
          sfConfig::get('sf_lib_dir').'/model/doctrine',
          Doctrine_Core::MODEL_LOADING_CONSERVATIVE
        );
        Doctrine_Core::createTablesFromArray(Doctrine_Core::getLoadedModels());

        self::$_dbRebuilt = true;
      }
      else
      {
        /* Determine the order we need to load models. */
        if( ! isset(self::$_dbFlushTree) )
        {
          Doctrine_Core::loadModels(
            sfConfig::get('sf_lib_dir').'/model/doctrine',
            Doctrine_Core::MODEL_LOADING_CONSERVATIVE
          );

          /** @noinspection PhpUndefinedFieldInspection */
          $models = $this->_connection->unitOfWork->buildFlushTree(
            Doctrine_Core::getLoadedModels()
          );
          self::$_dbFlushTree = array_reverse($models);
        }

        /* Delete records, paying special attention to SoftDelete. */
        $connectionsPreserved = sfConfig::get('app_sfJwtPhpUnitPlugin_connections_not_flushed_data');

        foreach( self::$_dbFlushTree as $model )
        {
          $table = Doctrine_Core::getTable($model);
          $tableConnectionName = $table->getConnection()->getName();

          if (!in_array($tableConnectionName, $connectionsPreserved))
          {
            $table->getConnection()->getDbh()->query("SET FOREIGN_KEY_CHECKS=0;");

            if( $table->hasTemplate('SoftDelete') )
            {
              /** @var $record Doctrine_Template_SoftDelete */
              foreach( $table->createQuery()->execute() as $record )
              {
                $record->hardDelete();
              }
            }

            $table->createQuery()->delete()->execute();
            $table->clear();

            $table->getConnection()->getDbh()->query("SET FOREIGN_KEY_CHECKS=1;");
          }
        }
      }
    }

    return $this;
  }

  /** Removes anything in the uploads directory.
   *
   * @return static
   */
  public function flushUploads(  )
  {
    $Filesystem = new sfFilesystem();
    $Filesystem->remove(
      sfFinder::type('any')->in(sfConfig::get('sf_upload_dir'))
    );

    return $this;
  }

  /** Restores all sfConfig values to their state before the current test was
   *   run.
   *
   * @return static
   */
  public function flushConfigs(  )
  {
    if( isset(self::$_configs) )
    {
      sfConfig::clear();
      sfConfig::add(self::$_configs);
    }
    else
    {
      self::$_configs = sfConfig::getAll();
    }

    return $this;
  }

  /** Gets the Doctrine connection, initializing it if necessary.
   *
   * @return Doctrine_Connection
   */
  protected function getDatabaseConnection(  )
  {
    if( sfConfig::get('sf_use_database') )
    {
      try
      {
        return Doctrine_Manager::connection();
      }
      catch( Doctrine_Connection_Exception $e )
      {
        new sfDatabaseManager($this->_configuration);
        return Doctrine_Manager::connection();
      }
    }

    return null;
  }

  /** Returns the name of the Doctrine database.
   *
   * @return string
   */
  protected function getDatabaseName(  )
  {
    /* Why oh why does Doctrine_Connection not do this for us? */
    if( ! $dsn = $this->_connection->getOption('dsn') )
    {
      throw new RuntimeException(sprintf(
        'Doctrine connection "%s" does not have a DSN!',
          $this->_connection->getName()
      ));
    }

    /** @noinspection PhpParamsInspection */
    $info = $this->_connection->getManager()->parsePdoDsn($dsn);
    return (isset($info['dbname']) ? $info['dbname'] : null);
  }
}