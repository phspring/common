<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine;

use PhSpring\Annotations\AccessControl;
use PhSpring\Annotations\RequestMapping;
use PhSpring\Engine\ClassInvoker;
use PhSpring\Engine\Handler\AccessControlHandler;
use PhSpring\Engine\Handler\RequestMappingHandler;
use PhSpring\Engine\IRequestHelper;
use PhSpring\Engine\RequestHelper;
use PhSpring\Reflection\ReflectionMethod;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * Description of InvokerConfig
 *
 * @author lobiferi
 */
class InvokerConfig {

    private static $beforeHandlers = array(
        AccessControl::class => AccessControlHandler::class,
        RequestMapping::class => RequestMappingHandler::class,
        Valid::class => ValidHandler::class,
    );
    private static $afterHandlers = array(
    );
    private static $requestHelper;
    private static $responseHelper;
    private static $annotationHandlerNamespaces = array('PhSpring\Engine\Handler');

    public static function getMethodBeforeHandlers(ReflectionMethod $reflMethod) {
        $ret = array();
        foreach ($reflMethod->getAnnotations() as $annotation) {
            $clazz = null;
            $aclass = get_class($annotation);
            if (array_key_exists($aclass, self::$beforeHandlers)) {
                $clazz = self::$beforeHandlers[$aclass];
            }
            if (!$clazz) {
                $bclass = \explode(\NAMESPACE_SEPARATOR, $aclass);
                $bclass = end($bclass);
                //die();
                foreach (self::$annotationHandlerNamespaces as $ns) {
                    $class = trim($ns, NAMESPACE_SEPARATOR).  NAMESPACE_SEPARATOR . $bclass.'Handler';
                    try {
                        if (class_exists($class)) {
                            $clazz = $class;
                            self::$beforeHandlers[$aclass] = $class;
                            break;
                        }
                    } catch (\Exception $e) {
                        //do nothing
                    }
                }
            }
            if ($clazz) {
                $ret[] = ClassInvoker::getNewInstance($clazz, array('annotation' => $annotation));
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
     * @param IRequestHelper $helper
     */
    public static function setRequestHelper(IRequestHelper $helper) {
        self::$requestHelper = $helper;
    }

    /**
     * @return IResponseHelper
     */
    public static function getResponseHelper() {
        if (self::$responseHelper === null) {
            self::$responseHelper = new ResponseHelper();
        }
        return self::$responseHelper;
    }

    /**
     * 
     * @param IResponseHelper $helper
     */
    public static function setResponseHelper(IResponseHelper $helper) {
        self::$responseHelper = $helper;
    }

}
