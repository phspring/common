<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine;

use PhSpring\Annotation\Helper as AnnotationHelper;
use PhSpring\Annotations\Autowired;
use ReflectionClass;
use RuntimeException;

/**
 * Description of Autowired
 *
 * @author lobiferi
 */
class ClassInvoker {

    /**
     * @param ReflectionClass $reflClass
     * @param array $params
     * @return object instance of $reflClass
     * @throws RuntimeException
     */
    public static function getNewInstanceByRefl(ReflectionClass $reflClass, array $params = null) {
        $instance = $reflClass->newInstanceWithoutConstructor();
        $className = $reflClass->getName();
        foreach ($reflClass->getProperties() as $property) {
            if (AnnotationHelper::hasAnnotation($property, Autowired::class)) {
                InvokerConfig::getAnnotationHandler(Autowired::class)->run($property, $instance);
            }
        }

        if ($reflClass->hasMethod('__construct')) {
            if ($reflClass->getMethod("__construct")->isPublic()) {
                MethodInvoker::invoke($instance, '__construct', $params);
            } else {
                throw new RuntimeException("The constructor is not public in {$className} class");
            }
        }
        return $instance;
    }

    /**
     * @param ReflectionClass|string $className
     * @param array $params constructor parameters
     * @return object instanceof the class
     * @throws RuntimeException
     */
    public static function getNewInstance($className, array $params = null) {
        if ($className instanceof ReflectionClass) {
            return self::getNewInstanceByRefl($className, $params);
        } elseif (is_string($className)) {
            return self::getNewInstanceByRefl(new ReflectionClass($className), $params);
        }
        throw new RuntimeException("The className parameter must be string or instanceof ReflectionClass");
    }

}
