<?php
/** Base functionality for PHPUnit-related tasks.
 *
 * @package jwt
 * @subpackage lib.task
 */
abstract class BasePhpunitTask extends sfBaseTask
{
  protected
    $_type   = '',
    $_extras = array();

  public function configure(  )
  {
    $this->namespace = 'phpunit';

    $this->addOptions(array(
      new sfCommandOption(
        'application',
        'a',
        sfCommandOption::PARAMETER_REQUIRED,
        'Run tests from the specified application.',
        'frontend'
      ),

      new sfCommandOption(
        'filter',
        'f',
        sfCommandOption::PARAMETER_REQUIRED,
        'Regex used to filter tests; only tests matching the filter will be run.',
        null
      ),

      new sfCommandOption(
        'verbose',
        'v',
        sfCommandOption::PARAMETER_REQUIRED,
        'If set to 1, PHPUnit will output additional information (e.g. test names).',
        null
      )
    ));
  }

  public function execute( $args = array(), $opts = array() )
  {
    $this->_runTests(
      $this->_type,
      $this->_validatePhpUnitInput($args, $opts)
    );
  }

  /** Runs all tests of a given type.
   *
   * @param string $type    ('unit', 'functional', '') If empty, runs all tests.
   * @param array  $options
   *
   * @return void
   */
  protected function _runTests( $type = '', array $options = array() )
  {
    /* Ensure we have an application configuration.
     *
     * Note that the application option is required (but defaults to
     *  'frontend').
     */
    if( ! sfContext::hasInstance() )
    {
      $init = sfConfig::get('sf_root_dir') . '/test/bootstrap/phpunit.php';
      if( is_file($init) )
      {
        $Harness = new Test_Harness($init);
        $Harness->execute();
      }

      if( $this->configuration instanceof sfApplicationConfiguration )
      {
        sfContext::createInstance($this->configuration);
      }
      elseif( $this->configuration instanceof ProjectConfiguration )
      {
        sfContext::createInstance(
          $this->configuration->getApplicationConfiguration(
            $options['application'],
            'test',
            true,
            sfConfig::get('sf_root_dir')
          )
        );
      }
    }
    unset($options['application']);

    $basedir = sfConfig::get('sf_plugins_dir') . '/sfJwtPhpUnitPlugin';
    require_once $basedir . '/test/bootstrap/phpunit.php';

    /* Do not list infrastructure directories in test failure backtraces. */
    $blacklist = array(
      $basedir . '/lib/test',
      realpath(dirname(__FILE__) . '/..'),
      sfCoreAutoload::getInstance()->getBaseDir()
    );
    foreach( $blacklist as $dir )
    {
      PHPUnit_Util_Filter::addDirectoryToFilter($dir);
    }

    PHPUnit_Util_Filter::addFileToFilter(
      sfConfig::get('sf_root_dir') . '/symfony'
    );

    require_once 'PHPUnit/TextUI/TestRunner.php';
    $Runner = new PHPUnit_TextUI_TestRunner();

    $Suite = new PHPUnit_Framework_TestSuite(ucfirst($this->_type) . ' Tests');
    $Suite->addTestFiles($this->_findTestFiles($type));

    try
    {
      $Runner->doRun($Suite, $options);
    }
    catch( PHPUnit_Framework_Exception $e )
    {
      $this->logSection('phpunit', $e->getMessage());
    }
  }

  /** Generates a list of test files.
   *
   * @param string $type ('unit', 'functional') If empty, all tests returned.
   *
   * @return array(string)
   */
  protected function _findTestFiles( $type = '' )
  {
    if( $type == '' )
    {
      return array_merge(
        $this->_findTestFiles('unit'),
        $this->_findTestFiles('functional')
      );
    }
    else
    {
      $base = sfConfig::get('sf_root_dir') . '/test/';

      return sfFinder::type('file')
        ->name('*.php')
        ->in($base . $type);
    }
  }

  /** Compiles arguments and options into a single array.
   *
   * @param array $args
   * @param array $opts     Note:  in a conflict, options override arguments.
   * @param array $defaults
   *
   * @return array
   */
  protected function _validateInput( array $args, array $opts, array $defaults = array() )
  {
    return array_merge(
      $defaults,
      array_filter($args, array($this, '_isset')),
      array_filter($opts, array($this, '_isset'))
    );
  }

  /** Extracts PHPUnit-specific arguments/options.
   *
   * @param array $args
   * @param array $opts
   *
   * @return array
   */
  protected function _validatePhpUnitInput( array $args, array $opts )
  {
    $allowed = array(
      'application' => 'frontend',
      'colors'      => true,
      'filter'      => null,
      'verbose'     => false
    );

    $params = array_intersect_key(
      $this->_validateInput($args, $opts, $allowed),
      $allowed
    );

    foreach( $params as $key => &$val )
    {
      if( isset($allowed[$key]) )
      {
        settype($val, gettype($allowed[$key]));
      }
    }

    return $params;
  }

  /** Used as a callback to array_filter() _validateInput() for PHP < 5.3.
   *
   * @param mixed $val
   *
   * @return bool
   */
  protected function _isset( $val )
  {
    return isset($val);
  }
}