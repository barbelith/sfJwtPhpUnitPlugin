<?php
/** Runs all functional tests for a project.
 *
 * @package sfJwtPhpUnitPlugin
 * @subpackage lib.task
 */
class FunctionalTestsTask extends BasePhpunitTask
{
  public function configure(  )
  {
    parent::configure();

    $this->addArguments(array(
      new sfCommandArgument(
        'path',
        sfCommandArgument::OPTIONAL | sfCommandArgument::IS_ARRAY,
        'Specify the relative paths to specific test files and/or directories under sf_root_dir/test/functional.  If no arguments are provided, all functional tests will be run.',
        null
      )
    ));

    $this->name = 'functional';
    $this->briefDescription = 'Runs all PHPUnit functional tests for the project.';

    $this->detailedDescription = <<<END
Runs PHPUnit functional tests for the project.
END;

    $this->_type = 'functional';
  }

  public function execute( $args = array(), $opts = array() )
  {
    $params       = $this->_validateInput($args, $opts);
    $this->_paths = (array) $params['path'];

    $this->_runTests($this->_validatePhpUnitInput($args, $opts));
  }
}