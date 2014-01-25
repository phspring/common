<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine;

use BadMethodCallException;
use Phoore\Annotation\Helper;
use PhSpring\Annotation\Helper as AnnotationHelper;
use PhSpring\Annotations\Autowired as AutowiredAnnotation;
use PhSpring\Annotations\RequestParam;
use PhSpring\Engine\Constants;
use PhSpring\Engine\Handler\InvokeParameterHandler;
use PhSpring\Service\Helper as ServiceHelper;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use Zend_Controller_Action;
use Zend_Controller_Request_Http;

/**
 * Description of MethodInvoker
 *
 * @author lobiferi
 */
class MethodInvoker {

    /**
     *
     * @var Helper
     */
    private static $helper;

    /**
     * 
     * @return Helper
     */
    private static function getHelper() {
        if (self::$helper === null) {
            self::$helper = AnnotationHelper::getInstance();
        }
        return self::$helper;
    }

    public static function invokeMethod($instance, $method, $args, $context = null) {
        $reflClass = new ReflectionClass(get_class($instance));
        if (!$reflClass->hasMethod($method)) {
            throw new BadMethodCallException();
        }
        $reflMethod = $reflClass->getMethod($method);
        
        $contextParams = array();
        $contextParams[Constants::CONTEXT_PARAM_CONTEXT] = $instance;
        $contextParams[Constants::CONTEXT_PARAM_METHOD] = $method;
        $contextParams[Constants::CONTEXT_PARAM_ARGS] = $args;
        $contextParams[Constants::CONTEXT_PARAM_REFL_METHOD] = $reflMethod;
        $contextParams[Constants::CONTEXT_PARAM_REFL_CLASS] = $reflClass;
        $annotations = AnnotationHelper::getInstance()->getMethodAnnotations($reflMethod);
        foreach(InvokerConfig::getMethodBeforeHandlers($reflMethod, $annotations) as $methodAnnotationHandler){
            $methodAnnotationHandler->run($reflMethod, $instance);
        }
        $invokeParams = (new InvokeParameterHandler($annotations->copyToArray(), $reflMethod, $args))->run();
        return $reflMethod->invokeArgs($instance, $invokeParams);
    }

    private static function getInvokeParams(ReflectionMethod $reflMethod, array $args = null, $instance) {
        if (!is_array($args)) {
            $args = (array) $args;
        }
        $invokeParams = array_values($args);
        if (self::getHelper()->hasAnnotation($reflMethod, AutowiredAnnotation::class)) {
            foreach ($reflMethod->getParameters() as $parameter) {
                $parameterName = $parameter->getName();
                $type = self::getParameterType($parameter);
                if (array_key_exists($parameterName, $args)) {
                    $invokeParams[$parameter->getPosition()] = $args[$parameterName];
                    continue;
                }
                if (array_key_exists($parameter->getPosition(), $args)) {
                    $invokeParams[$parameter->getPosition()] = $args[$parameter->getPosition()];
                    continue;
                }

                $isPrimitiveType = (in_array($type, Constants::$php_default_types) || in_array($type, Constants::$php_pseudo_types));
                if (($isPrimitiveType || $type===null) && ($instance instanceof Zend_Controller_Action)) {
                    self::handleRequestParam($parameter, $instance, $invokeParams);
                } elseif (!$isPrimitiveType && $type) {
                    $invokeParams[$parameter->getPosition()] = ServiceHelper::getService($type);
                }else{
                    $invokeParams[$parameter->getPosition()] = null;
                }
            }
        }
        return $invokeParams;
    }

    private static function handleRequiredRequestParam(ReflectionParameter $parameter, Zend_Controller_Request_Http $request, RequestParam $annotation) {
        $params = $request->getParams();
        $parameterName = $parameter->getName();
        if ($annotation->required) {
            if ((!array_key_exists($parameterName, $params) || $params[$parameterName] === null) && !$parameter->isOptional()) {
                throw new Exception("Parameter not found in the request: {$parameterName}");
            } elseif ($parameter->isOptional()) {
                $request->setParam($parameterName, $parameter->getDefaultValue());
            }
        }
    }

    private static function handleRequestParam(ReflectionParameter $parameter, Zend_Controller_Action $object, array &$invokeParams) {
        $reflMethod = $parameter->getDeclaringFunction();
        $parameterName = $parameter->getName();
        if (
                self::getHelper()->hasAnnotation(
                        $reflMethod, RequestParam::class, array('value' => $parameterName)
                )
        ) {
            $annotation = self::getHelper()->getMethodAnnotation($reflMethod, RequestParam::class, array('value' => $parameterName));


            self::handleRequiredRequestParam($parameter, $object->getRequest(), $annotation);
            $value = $object->getRequest()->getParam($parameterName);
            if ($annotation->defaultValue !== '****UNDEFINED****' && $value === null) {
                $value = $annotation->defaultValue;
            }
            $invokeParams[$parameter->getPosition()] = $value;
        }
    }


}
