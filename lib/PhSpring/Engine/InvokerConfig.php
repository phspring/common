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

    public static function getMethodBeforeHandlers($reflMethod) {
        $ret = array();
        foreach (AnnotationHelper::getInstance()->getMethodAnnotations($reflMethod) as $annotation) {
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

}
