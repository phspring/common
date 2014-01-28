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

/**
 * Description of Autowired
 *
 * @author lobiferi
 */
class ClassInvoker {

    public static function getNewInstanceByRefl(ReflectionClass $reflClass, array $params = null) {
        $instance = $reflClass->newInstanceWithoutConstructor();
        foreach ($reflClass->getProperties() as $property) {
            if (AnnotationHelper::hasAnnotation($property, Autowired::class)) {
                AnnotationHelper::getAnnotationHandler(Autowired::class)->run($property, $instance);
            }
        }

        if ($reflClass->hasMethod('__construct')) {
            MethodInvoker::invoke($instance, '__construct', $params);
        }
        return $instance;
    }

    public static function getNewInstance($className, array $params = null) {
        return self::getNewInstanceByRefl(new ReflectionClass($className), $params);
    }
    
}
