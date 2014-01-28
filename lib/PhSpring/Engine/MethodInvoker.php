<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine;

use BadMethodCallException;
use PhSpring\Annotation\Helper as AnnotationHelper;
use PhSpring\Engine\Handler\InvokeParameterHandler;
use ReflectionClass;

/**
 * Description of MethodInvoker
 *
 * @author lobiferi
 */
class MethodInvoker {

    public static function invoke($instance, $method, $args) {
        $reflClass = new ReflectionClass(get_class($instance));
        if (!$reflClass->hasMethod($method)) {
            throw new BadMethodCallException();
        }
        $reflMethod = $reflClass->getMethod($method);
        $annotations = AnnotationHelper::getAnnotations($reflMethod);
        foreach (InvokerConfig::getMethodBeforeHandlers($reflMethod, $annotations) as $methodAnnotationHandler) {
            $methodAnnotationHandler->run($reflMethod, $instance);
        }
        $invokeParams = (new InvokeParameterHandler($annotations, $reflMethod, $args))->run();
        return $reflMethod->invokeArgs($instance, $invokeParams);
    }

}
