<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Reflection;

use InvalidArgumentException;
use PhSpring\Annotation\Helper;
use ReflectionMethod as OriginReflectionMethod;
use ReflectionClass as OriginReflectionClass;
/**
 * Description of ReflectionMethod
 *
 * @author lobiferi
 */
class ReflectionMethod extends OriginReflectionMethod {

    private $annotations = false;

    /**
     *
     * @var string
     */
    private static $adapterClass = null;

    /**
     *
     * @var ReflectionMethod
     */
    private $adapter = null;

    public function __construct($class, $name) {
        if (self::$adapterClass !== null) {
            $this->adapter = new self::$adapterClass($class, $name);
        } else {
            parent::__construct($class, $name);
        }
    }

    public function getAnnotation($name, $values = null) {
        return Helper::getAnnotation($this,$name, $values);
    }

    public function getAnnotations() {
        if ($this->annotations === false) {
            $this->annotations = Helper::getAnnotations($this);
        }
        return $this->annotations;
    }

    public function hasAnnotation($name, $values = null) {
        return $this->getAnnotation($name, $values) !== null;
    }

    public function getDeclaringClass() {
        if ($this->adapter !== null) {
            $class = call_user_func(array($this->adapter, __FUNCTION__));
        } else {
            $class = call_user_func(array('parent', __FUNCTION__));
        }
        return new ReflectionClass($class->getName());
    }

    
    public static function setReflectionAdapterClass($className) {
        if ($className === OriginReflectionMethod::class || (new OriginReflectionClass($className))->isSubclassOf(OriginReflectionMethod::class)) {
            self::$adapterClass = $className;
        } else {
            throw new InvalidArgumentException("$className must be extend of \ReflectionClass - " . OriginReflectionMethod::class);
        }
    }

    
    
    public function __call($name, $arguments) {
        if ($this->adapter !== null) {
            return call_user_func_array(array($this->adapter, $name), $arguments);
        }
        if ($name !== __FUNCTION__) {
            return call_user_func_array(array('parent', $name), $arguments);
        }
    }

    public static function __callStatic($name, $arguments) {
        if (self::$adapterClass !== null) {
            return call_user_func_array(array(self::$adapterClass, $name), $arguments);
        }

        if ($name !== __FUNCTION__) {
            return call_user_func_array(array(self::class, $name), $arguments);
        }
    }

    public function __toString() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public static function export($class, $name, $return = false) {
        return self::__callStatic(__FUNCTION__, func_get_args());
    }

    public function getClosure($object) {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function getClosureScopeClass() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function getClosureThis() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function getDocComment() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function getEndLine() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function getExtension() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function getExtensionName() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function getFileName() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function getModifiers() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function getName() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function getNamespaceName() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function getNumberOfParameters() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function getNumberOfRequiredParameters() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function getParameters() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function getPrototype() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function getShortName() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function getStartLine() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function getStaticVariables() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function inNamespace() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function invoke($object, $parameter = null, $_ = null) {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function invokeArgs($object, array $args) {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function isAbstract() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function isClosure() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function isConstructor() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function isDeprecated() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function isDestructor() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function isFinal() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function isGenerator() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function isInternal() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function isPrivate() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function isProtected() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function isPublic() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function isStatic() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function isUserDefined() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function returnsReference() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function setAccessible($accessible) {
        return $this->__call(__FUNCTION__, func_get_args());
    }

}
