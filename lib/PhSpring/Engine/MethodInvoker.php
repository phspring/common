<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine;

use PhSpring\Engine\Handler\InvokeParameterHandler;
use PhSpring\Reflection\ReflectionClass;
use Symfony\Component\Validator\Exception\BadMethodCallException;

/**
 * Description of MethodInvoker
 *
 * @author lobiferi
 */
class MethodInvoker {

    public static function invoke($instance, $methodName, $args = array()) {
        $className = get_class($instance);
        $reflClass = new ReflectionClass($className);
        if (!$reflClass->hasMethod($methodName)) {
            throw new BadMethodCallException("The '{$methodName}' method is not exists in {$className}");
        }
        $reflMethod = $reflClass->getMethod($methodName);
        $annotations = $reflMethod->getAnnotations();

        foreach (InvokerConfig::getMethodBeforeHandlers($reflMethod) as $methodAnnotationHandler) {
            $methodAnnotationHandler->run($reflMethod, $instance);
        }
        $expectedParameterSize = sizeof($reflMethod->getParameters());
        $invokeParams = (new InvokeParameterHandler($annotations, $reflMethod, $args))->run();
        if ($expectedParameterSize > sizeof($invokeParams)) {
            throw new BadMethodCallException("Not found all expected method parameter: {$className}::{$methodName}()");
        }
        return $reflMethod->invokeArgs($instance, $invokeParams);
    }

}
