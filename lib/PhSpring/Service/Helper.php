<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Service;

use PhSpring\Annotation\Helper as AnnotationHelper;
use PhSpring\Annotations\Component;
use PhSpring\Annotations\Lazy;
use PhSpring\Annotations\Qualifier;
use PhSpring\Annotations\Scope;
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
                self::addNewServiceInstance($type, $serviceName);
                throw new RuntimeException("Not defined service! - name: '{$serviceName}'");
            }
            if ($type !== null && !($service instanceof $type)) {
                throw new RuntimeException("Found service class mismatch with expected type! - name: '{$serviceName}' type: '{$type}'");
            }
            return $service;
        }
        $service = Collection::get($type);
        if ($service === null) {
            self::addNewServiceInstance($type);
            $service = Collection::get($type);
        }
        return $service;
    }

    private static function addNewServiceInstance($type, $return = true) {
        $reflClass = new ReflectionClass($type);
        $lazy = $reflClass->hasAnnotation(Lazy::class);
        $scope = self::getScope($reflClass);

        if ($scope === Scope::SINGLETON) {
            if ($lazy && $return === false) {
                self::addLazyInitService($reflClass);
            } else {
                self::addSingleton($reflClass);
            }
        } else {
            self::addPrototypeService($reflClass);
        }
    }

    private static function addPrototypeService(ReflectionClass $reflClass) {
        Collection::add(self::getName($reflClass), new PrototypeProxy($reflClass));
    }

    private static function addLazyInitService(ReflectionClass $reflClass) {
        Collection::add(self::getName($reflClass), new LazyProxy($reflClass));
    }

    private static function addSingleton(ReflectionClass $reflClass) {
        if ($reflClass->hasMethod('getInstance')) {
            $type = $reflClass->getName();
            $service = $type::getInstance();
        } else {
            $service = ClassInvoker::getNewInstanceByRefl($reflClass);
        }
        Collection::add(self::getName($reflClass), $service);
    }

    public static function getName(ReflectionClass $reflClass) {
        $serviceName = $reflClass->getName();
        $component = $reflClass->getAnnotation(Component::class);
        if ($component && $component->name) {
            $serviceName = $component->name;
        }
        return $serviceName;
    }

    private static function getScope(ReflectionClass $reflClass) {
        $scope = Scope::SINGLETON;
        if ($reflClass->hasAnnotation(Component::class)) {
            $annotation = $reflClass->getAnnotation(Component::class);
            $scope = $annotation->scope;

            if ($reflClass->hasAnnotation(Scope::class)) {
                $annotation = $reflClass->getAnnotation(Scope::class);
                $scope = $annotation->value;
            }
        }
        /**
         * @todo Need more work
         */
        switch ($scope) {
            case Scope::SINGLETON:
            case Scope::REQUEST:
            case Scope::SESSION:
                $scope = Scope::SINGLETON;
                break;
            default:
                $scope = Scope::PROTOTYPE;
                break;
        }
        return $scope;
    }

    /**
     * @param ReflectionProperty $refl
     * @return string
     */
    public static function getServiceName(ReflectionProperty $refl) {
        $annotation = AnnotationHelper::getAnnotation($refl, Qualifier::class);
        if ($annotation) {
            return $annotation->value;
        }
        return null;
    }

    public static function addServiceClass($className) {
        self::addNewServiceInstance($className, false);
    }
    
    public static function addServiceBuilderClass($className) {
        self::addNewServiceInstance($className, false);
    }
    
    

}
