<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Reflection;

use InvalidArgumentException;
use PhSpring\Annotation\Helper;
use ReflectionClass as OriginReflectionClass;
use ReflectionProperty as OriginReflectionProperty;

/**
 * Description of ReflectionProperty
 *
 * @author lobiferi
 */
class ReflectionProperty extends OriginReflectionProperty {

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
            $this->annotations = Helper::getPropertyAnnotations($this);
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
        if ($className === OriginReflectionProperty::class || (new OriginReflectionClass($className))->isSubclassOf(OriginReflectionProperty::class)) {
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

    public static function export($class, $name, $return = null) {
        return self::__callStatic(__FUNCTION__, func_get_args());
    }

    public function getDocComment() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function getModifiers() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function getName() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function getValue($object = null) {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function isDefault() {
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

    public function setAccessible($accessible) {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function setValue($object, $value = null) {
        return $this->__call(__FUNCTION__, func_get_args());
    }

}
