<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine;

use PhSpring\Annotations\Autowired as AutowiredAnnotation;
use PhSpring\Annotation\Helper as AnnotationHelper;
use PhSpring\Annotations\RequestParam;

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

    public static function getNewInstanceByRefl(\ReflectionClass $reflClass, array $params = null, array $contextParams = null) {
        if($reflClass->isInterface()){
            $x;
        }
        $instance = $reflClass->newInstanceWithoutConstructor();
        foreach ($reflClass->getProperties() as $property) {
            if (self::getHelper()->hasAnnotation($property, AutowiredAnnotation::class)) {
                AnnotationHelper::getAnnotationHandler(AutowiredAnnotation::class)->run($property, $instance);
            }
        }

        if ($reflClass->hasMethod('__construct')) {
            MethodInvoker::invokeMethod($instance, '__construct', $params, $contextParams);
        }
        return $instance;
    }

    public static function getNewInstance($className, array $params = null, array $contextParams = null) {
        return self::getNewInstanceByRefl(new \ReflectionClass($className), $params);
    }

}
