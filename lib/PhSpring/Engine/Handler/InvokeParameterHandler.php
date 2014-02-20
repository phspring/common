<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine\Handler;

use PhSpring\Annotation\Collection;
use PhSpring\Annotations\Autowired;
use PhSpring\Annotations\RequestParam;
use PhSpring\Engine\Constants;
use PhSpring\Engine\InvokerConfig;
use PhSpring\Service\Helper as ServiceHelper;
use PhSpring\Reflection\ReflectionMethod;
use ReflectionParameter;
use RuntimeException;

/**
 * Description of InvokeParameterHandler
 *
 * @author lobiferi
 */
class InvokeParameterHandler {

    /** @var Collection */
    private $annotations;

    /** @var ReflectionMethod  */
    private $reflMethod;

    /** @var array */
    private $args = null;

    /** @var array */
    private $invokeParams = array();

    /**
     * 
     * @param array $annotations
     * @param ReflectionMethod $reflMethod
     * @param array $args
     */
    public function __construct(Collection $annotations, ReflectionMethod $reflMethod, array $args = null) {
        $this->annotations = $annotations;
        $this->reflMethod = $reflMethod;
        $this->args = $args;
    }

    public function run() {
        if (!is_array($this->args)) {
            $this->args = (array) $this->args;
        }
        $this->invokeParams = array_values($this->args);
//        if ($this->annotations->hasAnnotation(Autowired::class)) {
        foreach ($this->reflMethod->getParameters() as $parameter) {
            $this->setupParameterValue($parameter);
        }
//        }
        return $this->invokeParams;
    }

    private function setupParameterValue(ReflectionParameter $parameter) {
        $parameterName = $parameter->getName();
        $type = $this->getParameterType($parameter);
        if (array_key_exists($parameterName, $this->args)) {
            $this->invokeParams[$parameter->getPosition()] = $this->args[$parameterName];
            return;
        }
        if (array_key_exists($parameter->getPosition(), $this->args)) {
            $this->invokeParams[$parameter->getPosition()] = $this->args[$parameter->getPosition()];
            return;
        }

        $isPrimitiveType = (in_array($type, Constants::$php_default_types) || in_array($type, Constants::$php_pseudo_types));
        if ($parameter->isOptional()) {
            $this->invokeParams[$parameter->getPosition()] = $parameter->getDefaultValue();
        }


        if ($isPrimitiveType || $type === null) {
            $this->handleRequestParam($parameter, $this->invokeParams);
        } elseif (!$isPrimitiveType && $type) {
            $this->invokeParams[$parameter->getPosition()] = ServiceHelper::getService($type);
        }
    }

    /**
     * Return with the parameter type (string, int, ..., class name)
     * @param ReflectionParameter $parameter
     * @return null|string 
     */
    private function getParameterType(ReflectionParameter $parameter) {
        if ($parameter->getClass()) {
            return $parameter->getClass()->getName();
        }
        $matches = null;
        if (preg_match('/@param (\S*) \$' . $parameter->getName() . '/m', $this->reflMethod->getDocComment(), $matches)) {
            $types = preg_split('/|/', $matches[1]);
            $type = array_pop($types);
            return empty($type)?null:$type;
        }
        return null;
    }

    /**
     * 
     * @param ReflectionParameter $parameter
     * @void
     */
    private function handleRequestParam(ReflectionParameter $parameter) {
        $parameterName = $parameter->getName();
        if ($this->annotations->hasAnnotation(RequestParam::class, array('value' => $parameterName))) {
            $annotation = $this->annotations->getAnnotation(RequestParam::class, array('value' => $parameterName));
            $this->handleRequiredRequestParam($parameter, $annotation);
            $value = InvokerConfig::getRequestHelper()->getParam($parameterName);
            if ($annotation->defaultValue !== '****UNDEFINED****' && $value === null) {
                $value = $annotation->defaultValue;
            }
            $this->invokeParams[$parameter->getPosition()] = $value;
        }
    }

    private function handleRequiredRequestParam(ReflectionParameter $parameter, RequestParam $annotation) {
        $requestHelper = InvokerConfig::getRequestHelper();
        $parameterName = $parameter->getName();
        if ($annotation->required) {
            if ($requestHelper->getParam($parameterName) === null && !$parameter->isOptional()) {
                throw new RuntimeException("Parameter not found in the request: {$parameterName}");
            } elseif ($parameter->isOptional()) {
                $requestHelper->setParam($parameterName, $parameter->getDefaultValue());
            }
        }
    }

}
