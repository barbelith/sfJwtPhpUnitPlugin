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

/** Extends browser request functionality.
 *
 * @author Phoenix Zerin <phoenix@todofixthis.com>
 *
 * @package sfJwtPhpUnitPlugin
 * @subpackage lib.test.browser.plugin
 *
 * Partial list of methods exposed for the encapsulated sfWebRequest object
 *  (other methods are available, but they are not read-only and are probably
 *  not useful for testing):
 *
 * @method string             getContentType()
 * @method string             getUri()
 * @method boolean            isAbsUri()
 * @method string             getUriPrefix()
 * @method string             getPathInfo()
 * @method string             getPathInfoPrefix()
 * @method string[]           getGetParameters()
 * @method string[]           getPostParameters()
 * @method string[]           getRequestParameters()
 * @method string             getReferer()
 * @method string             getHost()
 * @method string             getScriptName()
 * @method boolean            isMethod(string $method)
 * @method string             getPreferredCulture(array $cultures = null)
 * @method string[]           getLanguages()
 * @method string[]           getCharsets()
 * @method string[]           getAcceptableContentTypes()
 * @method boolean            isXmlHttpRequest()
 * @method string             getHttpHeader(string $name, string $prefix = 'http')
 * @method mixed              getCookie(string $name, mixed $defaultValue = null)
 * @method boolean            isSecure()
 * @method string             getRelativeUrlRoot()
 * @method string[]           getPathInfoArray()
 * @method string             getRequestFormat()
 * @method array              getFiles(string $key = null)
 * @method string             getGetParameter(string $name, string $default = null)
 * @method string             getPostParameter(string $name, string $default = null)
 * @method string             getUrlParameter(string $name, string $default = null)
 * @method string             getRemoteAddress()
 * @method string             getForwardedFor()
 * @method string[]           getRequestContext()
 * @method array              getOptions()
 * @method string[]           extractParameters(array $names)
 * @method string             getMethod()
 * @method sfParameterHolder  getParameterHolder()
 * @method sfParameterHolder  getAttributeHolder()
 * @method string             getAttribute(string $name, string $default = null)
 * @method boolean            hasAttribute(string $name)
 * @method string             getParameter(string $name, string $default = null)
 * @method boolean            hasParameter(string $name)
 * @method string             getContent()
 */
class Test_Browser_Plugin_Request extends Test_Browser_Plugin
{
  /** Returns the name of the accessor that will invoke this plugin.
   *
   * For example, if this method returns 'getMagic', then the plugin can be
   *  invoked in a test case by calling $this->_browser->getMagic().
   *
   * @return string
   */
  public function getMethodName(  )
  {
    return 'getRequest';
  }

  /** Returns a reference to the request object from the browser context.
   *
   * @return static
   */
  public function invoke(  )
  {
    if( ! $this->hasEncapsulatedObject() )
    {
      $this->setEncapsulatedObject($this->getBrowser()->getRequest());
    }

    return $this;
  }

  /** Returns whether the request was forwarded.
   *
   * @return bool
   */
  public function isForwarded(  )
  {
    return $this->getBrowser()->getContext()->getActionStack()->getSize() > 1;
  }

  /** Returns the stack entry that the request was forwarded to.
   *
   * @param $pos Position in the stack to reference.
   *
   * @return sfActionStackEntry|null Only returns a value if the request was
   *  forwarded.
   */
  public function getForwardedActionStackEntry( $pos = 'last' )
  {
    if( $this->isForwarded() )
    {
      /* @var $Stack sfActionStack */
      $Stack = $this->getBrowser()->getContext()->getActionStack();

      switch( $pos )
      {
        case 'last':  $Entry = $Stack->getLastEntry();  break;
        case 'first': $Entry = $Stack->getFirstEntry(); break;
        default:      $Entry = $Stack->getEntry($pos);  break;
      }

      return $Entry;
    }
  }

  /** Returns the action and module name that the request was forwarded to.
   *
   * @param $pos Position in the stack to reference.
   *
   * @return array|void Only returns a value if the request was forwarded.
   *
   *  array(
   *    'module'  => (string; module name),
   *    'action'  => (string; action name)
   *  )
   */
  public function getForwardedArray( $pos = 'last' )
  {
    if( $Entry = $this->getForwardedActionStackEntry($pos) )
    {
      return array(
        'module'  => $Entry->getModuleName(),
        'action'  => $Entry->getActionName()
      );
    }
  }

  /** Returns the action and module name that the request was forwarded to, in
   *   string format.
   *
   * @param $pos Position in the stack to reference.
   *
   * @return string|void e.g., "module/action" if the request was forwarded.
   */
  public function getForwardedString( $pos = 'last' )
  {
    if( $Entry = $this->getForwardedActionStackEntry($pos) )
    {
      return sprintf('%s/%s', $Entry->getModuleName(), $Entry->getActionName());
    }
  }
}