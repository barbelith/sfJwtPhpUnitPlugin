# 1.0.12
## Issues
- Resolved [#37] Added `phpunit:load-fixture` task.

## Major Changes
- JPUP now requires PHPUnit 3.6.6.
- Added `--stop-on-fail` option to runner tasks.

## Minor Changes
- Added alias `phpunit:func` for `phpunit:functional`.
- Added `Test_Case_Functional->assertFormIsValid()` and
  `->assertFormIsNotValid()`.
- Fixed irritating "can't set headers - output already sent" when error occurs
  during browser context initialization in functional tests (usually caused by
  browser listeners).
- Clear table identity maps when flushing the database.
- Minor bug fixes in `Test_Browser->genUrl()`.
- Removed `package.xml.tmpl`.
- Added notice for symfony-project.org visitors to `README.md`; the version of
  JPUP on Symfony's website is horribly out-of-date, and it is likely to remain
  that way for quite some time.
- Added strategies for enhancing performance and memory usage in `INSTALL.md`.

# 1.0.11
## Issues
- Resolved [#16] Automatically set and create expected uploads directory before
  running tests.

## Major Changes
- `Test_Browser->genUrl()` now removes default path prefix more reliably.
- Auto-detect test files whose filename ends with `.test.php`.

# 1.0.10
## Issues
- Resolved [#7] Provide full path to settings.yml file when misconfiguration is detected.

## Major Changes
- Allow passing route names and parameter arrays to `Test_Browser->call()`.
- Require `no_script_name` to be true before running functional tests.

# 1.0.9
## Issues
- [#33] Added `Test_Case->runTask()`.

## Major Changes
- Allow calling `loadFixture($fixture, $plugin)` and
  `loadProductionFixture($fixture, $plugin)` in test cases and PHP fixtures.
- `Test_Browser_Plugin_Form->invoke()` now returns an `sfForm` instance, added
  `$var` argument.
- Added `Test_Browser_Plugin_Var`.
- Removed from API: `Test_Case->verifyTestDatabaseConnection()`.
- Removed from API: `Test_Case->validateUploadsDir()`.

## Minor Changes
- Minor comment and documentation updates.

# 1.0.8
## Issues
- [#1] Expanded compatibility section.  There; I updated the README.
- [#3] Introduced $_plugin property for allowing a test case to configure itself
  for a particular plugin.
- [#3] Moved definition of `sf_fixture_dir` into `Test_Case` so that it can be
  modified more easily at runtime.
- Resolved [#28] Implemented Test_Case->loadProductionFixture() and added
  plugin-specific fixture loaders for good measure.

## Major Changes
- Refactored fixture loading so that plugin-specific and production fixtures can
  also be loaded from within PHP fixture files.
- Do not auto-load global fixtures anymore.

## Minor Changes
- Rewrote warning against running functional tests for multiple applications in
  the same test run to make it more comprehensive.
- Minor comment and documentation updates.

# 1.0.7
## Major Changes
- JPUP no longer recognizes the `--trace` option due to incompatibilities with
  PHPUnit 3.6.

## Minor Changes
- Added `Test_Browser_Listener_Callback` for injecting event handlers into
  browser execution.
- Check for `sfDoctrineGuardPlugin` before proceeding in
  `Test_Browser->signin()`.
- Set `error_reporting` automatically in some cases.

# 1.0.6
## Major Changes
- Fixed output buffering issues with PHPUnit 3.6 once and for all.

## Minor Changes
- Better formatting of `assertStatusCode()` failure message.
- Fixed IDE warnings, added `@method` declarations for object wrappers, fixed
  type hinting where inaccurate.
- Updated email address in `@author` tags.

# 1.0.5
## Issues
- Resolved [#17] Make generating skeleton tests for inherited methods an
  option.

## Major Changes
- Added support for PHPUnit 3.6, dropped support for earlier versions.
- Introduced `Test_Browser_Plugin_Logger`.
- Fixed issues when trying to run tests for multiple applications in the same
  test run.

## Minor Changes
- If `--trace` option is provided, display full stack traces in test
  failure/error reports.
- Log a message to `sfLogger` when the user is logged in via
  `Test_Browser->signin()`.
- Added changelog to package.xml template.

# 1.0.4
## Issues
- Resolved [#19] Need a changelog.
- Resolved [#6]  Remove MySQL dependency.
- [#3]:  Added --plugin option to runner tasks (generators still outstanding).

## Major Changes
- Added `Test_Browser->signin()`.
- Empty `--filter` value no longer breaks PHPUnit.
- Added error/status code to `assertStatusCode()` failure message.
- Added `sf_fixture_dir` `sfConfig` value (needs documentation).

## Minor Changes
- Added `__toString()` handler to `Test_ObjectWrapper`.
- Convert parameters to strings before sending them to `sfBrowser->call()`.
- Fixed typos in skeleton test case files.
- Minor documentation updates.

# 1.0.0
- Initial release.