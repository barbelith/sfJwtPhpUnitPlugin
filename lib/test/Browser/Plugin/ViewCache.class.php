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

/** Extends browser view cache functionality.
 *
 * @author Phoenix Zerin <phoenix@todofixthis.com>
 *
 * @package sfJwtPhpUnitPlugin
 * @subpackage lib.test.browser.plugin
 *
 * Partial list of methods exposed for the encapsulated sfViewCacheManager
 *  object (other methods are available, but they are not read-only and are
 *  probably not useful for testing):
 *
 * @method sfContext  getContext()
 * @method sfCache    getCache()
 * @method string     getCacheKeyVaryHeaderPart(string $internalUri, string $vary = '')
 * @method string     getCacheKeyHostNamePart(string $hostName = '')
 * @method boolean    withLayout(string $internalUri)
 * @method int        getLifeTime(string $internalUri)
 * @method int        getClientLifeTime(string $internalUri)
 * @method boolean    isContextual(string $internalUri)
 * @method string[]   getVary(string $internalUri)
 * @method boolean    isCacheable(string $internalUri)
 * @method boolean    isActionCacheable(string $moduleName, string $actionName)
 * @method string     get(string $internalUri)
 * @method boolean    has(string $internalUri)
 * @method int        getLastModified(string $internalUri)
 * @method int        getTimeout(string $internalUri)
 * @method boolean    hasPartialCache(string $module, string $action, string $cacheKey)
 * @method string     getPartialCache(string $module, string $action, string $cacheKey)
 * @method boolean    hasActionCache(string $uri)
 * @method string     getActionCache(string $uri)
 * @method string     getPageCache(string $uri)
 * @method string     getCurrentCacheKey()
 */
class Test_Browser_Plugin_ViewCache extends Test_Browser_Plugin
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
    return 'getViewCache';
  }

  /** Returns a reference to the view cache object from the browser context.
   *
   * Note:  If caching is disabled, this method will return null.
   *
   * @return static
   */
  public function invoke(  )
  {
    if( ! $this->hasEncapsulatedObject() )
    {
      $this->setEncapsulatedObject(
        $this->getBrowser()->getContext()->getViewCacheManager()
      );
    }

    return $this->hasEncapsulatedObject() ? $this : null;
  }

  /** Returns whether the specified URL has been cached properly.
   *
   * @param string $uri         If not specified, the browser object's current
   *  URI (cache key) will be used.
   * @param bool   $with_layout If set, with_layout setting must also match.
   *
   * @return bool
   */
  public function isUriCached( $uri = null, $with_layout = null )
  {
    if( ! $uri )
    {
      $uri = $this->getCurrentCacheKey();
    }

    if( $this->has($uri) )
    {
      if( ! ($with_layout === null or $this->withLayout($uri) == $with_layout) )
      {
        return false;
      }

      $cached = $this->getContent($uri);
      return $cached == $this->getBrowser()->getContent();
    }

    return false;
  }

  /** Returns the HTML content of a cached URI.
   *
   * @param string $uri If not specified, the browser object's current URI
   *  (cache key) will be used.
   *
   * @return string
   */
  public function getContent( $uri )
  {
    if( ! $uri )
    {
      $uri = $this->getCurrentCacheKey();
    }

    return $this->has($uri) ? unserialize($this->get($uri))->getContent() : '';
  }
}