<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine;

use PhSpring\Annotation\Helper as AnnotationHelper;
use PhSpring\Annotations\Autowired as AutowiredAnnotation;
use ReflectionClass;

/**
 * Description of Autowired
 *
 * @author lobiferi
 */
class ClassInvoker {

    /**
     *
     * @var Phoore\Annotation\Helper
     */
    private static $helper;

    /**
     * 
     * @return Phoore\Annotation\Helper
     */
    private static function getHelper() {
        if (self::$helper === null) {
            self::$helper = AnnotationHelper::getInstance();
        }
        return self::$helper;
    }

    public static function getNewInstanceByRefl(ReflectionClass $reflClass, array $params = null) {
        $instance = $reflClass->newInstanceWithoutConstructor();
        foreach ($reflClass->getProperties() as $property) {
            if (self::getHelper()->hasAnnotation($property, AutowiredAnnotation::class)) {
                AnnotationHelper::getAnnotationHandler(AutowiredAnnotation::class)->run($property, $instance);
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
