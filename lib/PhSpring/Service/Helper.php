<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Service;

use \PhSpring\Annotations\Autowired;
use \PhSpring\Annotation\Helper as AnnotationHelper;

/**
 * Description of Helper
 *
 * @author lobiferi
 */
class Helper {

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

    public static function getService($type, $serviceName = null) {
        if (!empty($serviceName)) {
            $service = Collection::get($serviceName);
            if($service == null){
                throw new \Exception("Not defined service! - name: '{$serviceName}'");
            }
        }
        $service = Collection::get($type);
        if ($service === null) {
            $reflClass = new \Zend_Reflection_Class($type);

            if ($reflClass->hasMethod('getInstance')) {
                $service = $type::getInstance();
                Collection::add($serviceName, $service);
                return $service;
            };
            $service = \PhSpring\Engine\ClassInvoker::getNewInstanceByRefl($reflClass);
            Collection::add($serviceName, $service);
        }
        return $service;
    }

    private static function generateProxy(\Zend_Reflection_Class $reflClass, $proxyClass) {
        $newClass = self::generateClass($reflClass);
        $filePath = self::getProxyClassFilePath($reflClass, $proxyClass);

        $classFile = new \Zend_CodeGenerator_Php_File();
        $classFile->setClass($newClass);
        $classFile->setFilename($filePath);
        $classFile->write();
    }

    /**
     * 
     * @param \Zend_Reflection_Class $reflClass
     * @return \PhSpring\Generator\Proxy
     */
    private static function generateClass(\Zend_Reflection_Class $reflClass) {
        $extendedClass = str_replace($reflClass->getNamespaceName(), '', $reflClass->getName());
        $newClass = new \PhSpring\Generator\Proxy();
        $newClass->setName($proxyClass);
        $newClass->setNamespace($reflClass->getNamespaceName());
        $newClass->setExtendedClass(trim($extendedClass, "\\"));
        return $newClass;
    }

    private static function getProxyClassFilePath(\Zend_Reflection_Class $reflClass, $proxyClass) {
        $fileName = trim(str_replace('\\', DIRECTORY_SEPARATOR, $reflClass->getNamespaceName()) . DIRECTORY_SEPARATOR . $proxyClass . '.php', DIRECTORY_SEPARATOR);
        $nameParts = preg_split('/[\\_]/', $fileName);
        $filePath = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'generated';

        for ($i = 0, $l = sizeof($nameParts); $i < $l; $i++) {
            $filePath.= DIRECTORY_SEPARATOR . $nameParts[$i];
        }
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0777, true);
        }
        return $filePath;
    }

}
