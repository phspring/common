<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Service;

use PhSpring\Annotation\Helper as AnnotationHelper;
use PhSpring\Annotations\Qualifier;
use PhSpring\Engine\ClassInvoker;
use PhSpring\Reflection\ReflectionClass;
use ReflectionProperty;
use RuntimeException;

/**
 * Description of Helper
 *
 * @author lobiferi
 */
class Helper {

    public static function getService($type, $serviceName = null) {
        if (!empty($serviceName)) {
            $service = Collection::get($serviceName);
            if ($service == null) {
                throw new RuntimeException("Not defined service! - name: '{$serviceName}'");
            }
            if($type !== null && !($service instanceof $type)){
                throw new RuntimeException("Found service class mismatch with expected type! - name: '{$serviceName}' type: '{$type}'");
            }
            return $service;
        }
        $service = Collection::get($type);
        if ($service === null) {
            $reflClass = new ReflectionClass($type);

            if ($reflClass->hasMethod('getInstance')) {
                $service = $type::getInstance();
                Collection::add($serviceName, $service);
                return $service;
            };
            $service = ClassInvoker::getNewInstanceByRefl($reflClass);
            Collection::add($serviceName, $service);
        }
        return $service;
    }

    /**
     * @param ReflectionProperty $refl
     * @return string
     */
    public static function getServiceName(ReflectionProperty $refl) {
        if ($annotation = AnnotationHelper::getAnnotation($refl, Qualifier::class)) {
            return $annotation->value;
        }
        return null;
    }

}
