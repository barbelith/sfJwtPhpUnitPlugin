<?php
/** Emulates multiple inheritence by wrapping an instance of an object.
 *
 * It's not true multiple inheritence, as the wrapper does not have access to
 *  protected properties/methods of the wrapped instance; the wrapper can only
 *  add or overwrite functionality.
 *
 * NB:  Note that all passthrough methods will silently fail if no object is
 *  attached.
 *
 * @package sfJwtPhpUnitPlugin
 * @subpackage lib.test
 */
abstract class Test_ObjectWrapper
{
  private
    $_encapsulatedObject,
    $_injectedMethods;

  /** Accessor for $_encapsulatedObject.
   *
   * @return object|null
   */
  public function getEncapsulatedObject(  )
  {
    return $this->_encapsulatedObject;
  }

  /** Modifier for $_encapsulatedObject.
   *
   * Note that this method will overwrite any existing encapsulated object!
   *
   * @param object|string(class)|null $Object
   *  Note:  If $Object is an instance of Test_ObjectWrapper, the object's
   *   encapsulated object will be used instead of the wrapper.
   *
   * @return Test_ObjectWrapper($this)
   * @throws InvalidArgumentException if $Object is of the wrong type.
   *
   * @access protected Should only be invoked by subclass.
   */
  protected function setEncapsulatedObject( $Object )
  {
    if( is_object($Object) )
    {
      $this->_encapsulatedObject =
        $Object instanceof self
          ? $Object->getEncapsulatedObject()
          : $Object;
    }
    elseif( is_string($Object) and class_exists($Object) )
    {
      $this->_encapsulatedObject = new $Object();
    }
    elseif( $Object === null )
    {
      $this->_encapsulatedObject = null;
    }
    else
    {
      throw new InvalidArgumentException(sprintf(
        'Invalid %s passed to %s().',
          is_object($Object) ? get_class($Object) : gettype($Object),
          __FUNCTION__
      ));
    }

    return $this;
  }

  /** Inject a dynamic method call into the object.
   *
   * @param string    $method     The name of the method.
   * @param callable  $callback   The callback that will be invoked when the
   *  method is called.
   *
   * @return Test_ObjectWrapper($this)
   * @throws InvalidArgumentException if $method is not overloadable or if
   *  $callback is not callable.
   *
   * @access protected Should only be invoked by subclass.
   */
  protected function injectDynamicMethod( $method, $callback )
  {
    /* Check to make sure the method is overloadable.
     *
     * The dynamic method will be invoked via __call(), so if a method already
     *  exists with this name, the injected method will never get invoked.
     *
     * Given how frustrating it can be to debug misfiring magic, it is probably
     *  more desirable to detect such situations and fail rather than to ignore
     *  them.
     */
    $Reflector = new ReflectionObject($this);
    if( $Reflector->hasMethod($method) )
    {
      throw new InvalidArgumentException(sprintf(
        '%s%s%s() is not overloadable.',
          $Reflector->getName(),
          $Reflector->getMethod($method)->isStatic() ? '::' : '->',
          $method
      ));
    }

    /* Check to make sure the specified callback is callable. */
    if( ! is_callable($callback) )
    {
      /* Try to convert $callback into a reasonably stringable value.
       *
       * We don't have to be super-specific here, as there is a relatively small
       *  subset of probable formats; for anything super-unusual (e.g., an array
       *  of integers or something else that is clearly not callable), it should
       *  be sufficient just to note the bizarre format.
       */
      if( is_array($callback) )
      {
        $anchor   = reset($callback);
        $function = next($callback);

        $callbackAsString = sprintf(
          'array(%s, %s)',
            is_string($anchor)
              ? $anchor
              : (is_object($anchor) ? get_class($anchor) : gettype($anchor)),
            is_string($function) ? $function : gettype($function)
        );
      }
      else
      {
        $callbackAsString =
          is_string($callback)
            ? $callback
            : gettype($callback);
      }

      throw new InvalidArgumentException(sprintf(
        'Callback (%s) is not callable.',
          $callbackAsString
      ));
    }

    if( ! is_array($this->_injectedMethods) )
    {
      $this->_injectedMethods = array();
    }

    $this->_injectedMethods[$method] = $callback;
    return $this;
  }

  /** Access an injected dynamic method, if it exists.
   *
   * @param string $method
   *
   * @return callback|null
   *
   * @access protected Should only be invoked by subclass.
   */
  protected function getInjectedMethod( $method )
  {
    if( $this->_injectedMethods and isset($this->_injectedMethods[$method]) )
    {
      return $this->_injectedMethods[$method];
    }
  }

  /** Returns whether $_encapsulatedObject has been set yet.
   *
   * @return bool
   */
  public function hasEncapsulatedObject(  )
  {
    return isset($this->_encapsulatedObject);
  }

  /** Pass-through for generic accessor.
   *
   * @param string $key
   *
   * @return mixed
   */
  public function __get( $key )
  {
    return
      $this->hasEncapsulatedObject()
        ? $this->getEncapsulatedObject()->$key
        : null;
  }

  /** Pass-through for generic modifier.
   *
   * @param string $key
   * @param mixed  $val
   *
   * @return mixed
   */
  public function __set( $key, $val )
  {
    return
      $this->hasEncapsulatedObject()
        ? $this->getEncapsulatedObject()->$key = $val
        : null;
  }

  /** Pass-through for isset() handler.
   *
   * @param string $key
   *
   * @return bool
   */
  public function __isset( $key )
  {
    return
      $this->hasEncapsulatedObject()
        ? isset($this->getEncapsulatedObject()->$key)
        : false;
  }

  /** Pass-through for unset() handler.
   *
   * @param string $key
   *
   * @return void
   */
  public function __unset( $key )
  {
    if( $this->hasEncapsulatedObject() )
    {
      unset($this->getEncapsulatedObject()->$key);
    }
  }

  /** Pass-through for generic method handler.
   *
   * @param string $meth
   * @param array  $args
   *
   * @return mixed
   */
  public function __call( $meth, $args )
  {
    if( $callable = $this->getInjectedMethod($meth) )
    {
      return call_user_func_array($callable, $args);
    }

    return
      $this->hasEncapsulatedObject()
        ? call_user_func_array(
            array($this->getEncapsulatedObject(), $meth),
            $args
          )
        : null;
  }
}