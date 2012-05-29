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

/** Adds Symfony functional test case functionality to PHPUnit's TestCase
 *   framework.
 *
 * Note:  Designed to work with Symfony 1.4.  Might not work properly with later
 *  versions of Symfony.
 *
 * @author Phoenix Zerin <phoenix@todofixthis.com>
 *
 * @package sfJwtPhpUnitPlugin
 * @subpackage lib.test
 */
abstract class Test_Case_Functional extends Test_Case
{
  /** @var Test_Browser */
  protected $_browser;

  /** Pre-test initialization.
   *
   * @return void
   */
  final protected function _init(  )
  {
    if( ! sfConfig::get('sf_no_script_name') )
    {
      self::_halt(sprintf(
        'Set the "no_script_name" property to true in %s.',
          $this->_getSettingsFilename()
      ));
    }

    $this->_browser = new Test_Browser();
  }

  /** Asserts that the response from the most recent request sent the correct
   *    HTTP status code.
   *
   * @param int|int[] $code     If array, must match one of these.
   * @param string    $message  Custom failure message (optional).
   *
   * @return void
   */
  protected function assertStatusCode( $code, $message = '' )
  {
    self::assertThat(
      $this->_browser,
      new Test_Constraint_StatusCodeEquals($code),
      $message
    );
  }

  /** Asserts that a form object is valid.
   *
   * @param sfForm  $form
   * @param string  $message  Custom failure message (optional).
   *
   * @return void
   */
  protected function assertFormIsValid( sfForm $form, $message = '' )
  {
    self::assertThat(
      $form,
      new Test_Constraint_FormIsValid(true),
      $message
    );
  }

  /** Asserts that a form object is not valid.
   *
   * @param sfForm  $form
   * @param string  $message  Custom failure message (optional).
   *
   * @return void
   */
  protected function assertFormIsNotValid( sfForm $form, $message = '' )
  {
    self::assertThat(
      $form,
      new Test_Constraint_FormIsValid(false),
      $message
    );
  }
}