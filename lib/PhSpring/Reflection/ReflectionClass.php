<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Reflection;

use InvalidArgumentException;
use PhSpring\Annotation\Collection;
use PhSpring\Annotation\Helper;
use ReflectionClass as OriginReflectionClass;

/**
 * Description of ReflectionClass
 *
 * @author lobiferi
 */
class ReflectionClass extends OriginReflectionClass {

    protected $annotations = false;

    /**
     *
     * @var string
     */
    private static $adapterClass = null;

    /**
     *
     * @var ReflectionClass
     */
    private $adapter = null;

    public function __construct($class) {
        if (self::$adapterClass !== null) {
            $this->adapter = new self::$adapterClass($class);
        }elseif($class instanceof OriginReflectionClass){
            $this->adapter = $class;
            self::$adapterClass = get_class($class);
        } else {
            parent::__construct($class);
        }
    }

    /**
     * get annotation of class
     *
     * @param string $name annotation name
     * @return string
     */
    public function getAnnotation($name, $values = null) {
        return $this->getAnnotations()->getAnnotation($name, $values);
    }

    /**
     * 
     * @return Collection
     */
    public function getAnnotations() {
        if ($this->annotations === false) {
            $this->annotations = Helper::getAnnotations($this);
        }

        return $this->annotations;
    }

    public function hasAnnotation($name, $values = null) {
        return $this->getAnnotation($name, $values) !== null;
    }

    public function getMethod($method) {
        return new ReflectionMethod($this->getName(), $method);
    }

    public function getMethods($filter = null) {
        if ($filter === null) {
            $filter = ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED |
                    ReflectionMethod::IS_PRIVATE | ReflectionMethod::IS_STATIC |
                    ReflectionMethod::IS_ABSTRACT | ReflectionMethod::IS_FINAL;
        }

        if ($this->adapter === null) {
            $methods = parent::getMethods($filter);
        } else {
            $methods = $this->adapter->getMethods($filter);
        }
        $retMethods = array();

        foreach ($methods as $method) {
            $retMethods[$method->name] = $this->getMethod($method->name);
        }

        return $retMethods;
    }

    public function getProperty($property) {
        return new ReflectionProperty($this->getName(), $property);
    }

    public function getProperties($filter = null) {
        if ($filter === null) {
            $filter = ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED |
                    ReflectionProperty::IS_PRIVATE | ReflectionProperty::IS_STATIC;
        }
        if ($this->adapter === null) {
            $properties = parent::getProperties($filter);
        } else {
            $properties = $this->adapter->getProperties($filter);
        }
        $retProperties = array();
        foreach ($properties as $prop) {
            $retProperties[$prop->name] = $this->getProperty($prop->name);
        }

        return $retProperties;
    }

    public static function setReflectionAdapterClass($className) {
        if ($className === OriginReflectionClass::class || (new OriginReflectionClass($className))->isSubclassOf(OriginReflectionClass::class)) {
            self::$adapterClass = $className;
        } else {
            throw new InvalidArgumentException("$className must be extend of \ReflectionClass - " . OriginReflectionClass::class);
        }
    }

    public function setReflectionAdapter(OriginReflectionClass $adapter) {
        $this->adapter = $adapter;
        self::$adapterClass = get_class($adapter);
    }

    public function __call($name, $arguments) {
        if ($this->adapter !== null) {
            return call_user_func_array(array($this->adapter, $name), $arguments);
        }
        if ($name !== '__call') {
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

    /**
     *  #############################################
     *  ###               OVERRIDES               ###
     *  #############################################
     */
    public function __toString() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public static function export($argument, $return = false) {
        return self::__callStatic(__FUNCTION__, func_get_args());
    }

    public function getConstant($name) {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function getConstants() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function getConstructor() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function getDefaultProperties() {
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

    public function getInterfaceNames() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function getInterfaces() {
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

    public function getParentClass() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function getShortName() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function getStartLine() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function getStaticProperties() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function getStaticPropertyValue($name, $def_value = null) {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function getTraitAliases() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function getTraitNames() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function getTraits() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function hasConstant($name) {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function hasMethod($name) {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function hasProperty($name) {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function implementsInterface($interface) {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function inNamespace() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function isAbstract() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function isCloneable() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function isFinal() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function isInstance($object) {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function isInstantiable() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function isInterface() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function isInternal() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function isIterateable() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function isSubclassOf($class) {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function isTrait() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function isUserDefined() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function newInstance($args, $_ = null) {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function newInstanceArgs(array $args = null) {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function newInstanceWithoutConstructor() {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    public function setStaticPropertyValue($name, $default = NULL) {
        return $this->__call(__FUNCTION__, func_get_args());
    }

}
