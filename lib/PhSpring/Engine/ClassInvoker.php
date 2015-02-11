<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine;

use PhSpring\ApplicationTimer;
use PhSpring\Annotation\Helper;
use PhSpring\Annotations\Autowired;
use PhSpring\Reflection\ReflectionClass;
use RuntimeException;

/**
 * Description of Autowired
 *
 * @author lobiferi
 */
class ClassInvoker {

    private static $counter = 0;

    /**
     * @param ReflectionClass $reflClass
     * @param array $params
     * @return object instance of $reflClass
     * @throws RuntimeException
     */
    public static function getNewInstanceByRefl(\ReflectionClass $reflClass, array $params = null) {
        $counter = self::$counter++;
        if (!($reflClass instanceof ReflectionClass)) {
            $reflClass = new ReflectionClass($reflClass);
        }
        $instance = $reflClass->newInstanceWithoutConstructor();
        self::callPropertyAnnotationHandlers($reflClass, $instance, $counter);
        self::callConstructor($reflClass, $instance, $params, $counter);
        return $instance;
    }

    static private function callPropertyAnnotationHandlers($reflClass, $instance, $counter) {
        foreach ($reflClass->getProperties() as $property) {
            $hasAnnotation = $property->hasAnnotation(Autowired::class);

            if ($hasAnnotation) {
                $annotation = $property->getAnnotation(Autowired::class);
                Helper::getAnnotationHandler(get_class($annotation))->run($property, $instance);
            }
        }
    }

    static private function callConstructor($reflClass, $instance, $params, $counter) {
        if ($reflClass->hasMethod('__construct')) {
            if ($reflClass->getMethod("__construct")->isPublic()) {
                MethodInvoker::invoke($instance, '__construct', $params);
            } else {
                $className = $reflClass->getName();
                throw new RuntimeException("The constructor is not public in {$className} class");
            }
        }
    }

    /**
     * @param ReflectionClass|string $className
     * @param array $params constructor parameters
     * @return object instanceof the class
     * @throws RuntimeException
     */
    public static function getNewInstance($className, array $params = null) {
        if ($className instanceof \ReflectionClass) {
            return self::getNewInstanceByRefl($className, $params);
        } elseif (is_string($className)) {
            return self::getNewInstanceByRefl(new ReflectionClass($className), $params);
        }
        throw new RuntimeException("The className parameter must be string or instanceof ReflectionClass");
    }

}
