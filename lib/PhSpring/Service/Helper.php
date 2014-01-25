<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Service;

use Exception;
use PhSpring\Engine\ClassInvoker;
use ReflectionClass;

/**
 * Description of Helper
 *
 * @author lobiferi
 */
class Helper {

    public static function getService($type, $serviceName = null) {
        if (!empty($serviceName)) {
            $service = Collection::get($serviceName);
            if($service == null){
                throw new Exception("Not defined service! - name: '{$serviceName}'");
            }
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

}
