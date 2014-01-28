<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine;

use PhSpring\Annotation\Helper as AnnotationHelper;
use PhSpring\Annotations\AccessControl;
use PhSpring\Annotations\RequestMapping;
use PhSpring\Engine\ClassInvoker;
use PhSpring\Engine\Handler\AccessControlHandler;
use PhSpring\Engine\Handler\RequestMappingHandler;
use PhSpring\Engine\IRequestHelper;
use PhSpring\Engine\RequestHelper;

/**
 * Description of InvokerConfig
 *
 * @author lobiferi
 */
class InvokerConfig {

    private static $beforeHandlers = array(
        AccessControl::class => AccessControlHandler::class,
        RequestMapping::class => RequestMappingHandler::class,
    );
    private static $afterHandlers = array(
    );
    private static $requestHelper;
    private static $annptationHandlerNamespaces = array('PhSpring\Engine\Handler');

    public static function getMethodBeforeHandlers($reflMethod) {
        $ret = array();
        foreach (AnnotationHelper::getAnnotations($reflMethod) as $annotation) {
            if (array_key_exists(get_class($annotation), self::$beforeHandlers)) {
                $ret[] = ClassInvoker::getNewInstance(self::$beforeHandlers[get_class($annotation)], array('annotation' => $annotation));
            }
        }
        return $ret;
    }

    /**
     * @return IRequestHelper
     */
    public static function getRequestHelper() {
        if (self::$requestHelper === null) {
            self::$requestHelper = new RequestHelper();
        }
        return self::$requestHelper;
    }

    /**
     * 
     * @param \PhSpring\Engine\IRequestHelper $helper
     */
    public static function setRequestHelper(IRequestHelper $helper) {
        self::$requestHelper = $helper;
    }

    public static function addAnnotationHandlerNamespace($namespace) {
        array_unshift(self::$annptationHandlerNamespaces, $namespace);
    }

    public static function getAnnotationHandler($annotationType) {
        if(is_object($annotationType)){
            $annotationType = get_class($annotationType);
        }
        $annotationType = explode('\\', $annotationType);
        $annotationType = end($annotationType);
        $found = false;
        foreach (self::$annptationHandlerNamespaces as $ns) {
            $handler = $ns . '\\'.$annotationType.'Handler';
            $file = str_replace('\\', DIRECTORY_SEPARATOR, $handler.'.php');
            if(class_exists($handler)){
                $found = $handler;
                break;
            }
        }
        if($found !== false){
            return ClassInvoker::getNewInstance($found);
        }
        else{
            return null;
        }
        
    }

}
