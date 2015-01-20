<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine;

use PhSpring\Annotations\ExpressionAnd;
use PhSpring\Annotations\ExpressionNot;
use PhSpring\Annotations\ExpressionOr;
use PhSpring\Annotations\IExpression;
use PhSpring\Annotations\RequestMapping;
use PhSpring\Annotations\RequestMethod;
use PhSpring\Reflection\ReflectionClass;
use PhSpring\Reflection\ReflectionMethod;
use PhSpring\Service\Helper;
use ReflectionMethod as PHP_ReflectionMethod;

/**
 * Description of RequestMappingHelper
 *
 * @author lobiferi
 */
class RequestMappingHelper {

    /**
     * 
     * @param \PhSpring\Annotations\RequestMapping $annotation
     * @return boolean
     */
    public static function isMatching(RequestMapping $annotation) {
        $methodType = self::getRequestMethodType();
        $checker = function($value)use($methodType, &$checker) {
            if (is_integer($value)) {
                return !!($value & $methodType);
            } elseif ($value instanceof IExpression) {
                return self::expression($value, $checker);
            }
            return false;
        };
        return $checker($annotation->method);
    }

    /**
     * @param object|class $controller
     * @return string Name of matchig method
     */
    public static function getMatchingMethod($controller) {
        $reflClass = new ReflectionClass($controller);
        $classAnnotation = $reflClass->getAnnotation(RequestMapping::class);
        $requestHelper = Helper::getService(HttpServletRequest::class);
        $pathInfo = $requestHelper->getServer(HttpServletRequest::PATH_INFO);
        if (!$pathInfo) {
            $pathInfo = $requestHelper->getServer(HttpServletRequest::REQUEST_URI);
        }
        /* @var $reflMethod ReflectionMethod */
        foreach ($reflClass->getMethods(PHP_ReflectionMethod::IS_PUBLIC) as $reflMethod) {
            /* @var $methodAnnotation RequestMapping */
            $methodAnnotation = $reflMethod->getAnnotation(RequestMapping::class);
            /* @var $requestHelper HttpServletRequest */
            $url = '';
            if ($classAnnotation) {
                $url .= $classAnnotation->value;
            }
            if ($methodAnnotation) {
                $url .= $methodAnnotation->value;
            }
            $regexp = '/^' . str_replace('/', '\\/', $url) . '/';
            $test = preg_match($regexp, $pathInfo, $prop);
            $test &= $methodAnnotation !== null && self::isMatching($methodAnnotation);
            if ($url && $methodAnnotation && $test) {
                foreach ($prop as $key => $value) {
                    if (!is_numeric($key)) {
                        $requestHelper->setParam($key, $value);
                    }
                }
                return $reflMethod->getName();
            }
        }
        return null;
    }

    /**
     * @return integer Integer representation of request type
     */
    private static function getRequestMethodType() {
        $methodType = Helper::getService(HttpServletRequest::class)->getMethod();
        if (!is_int($methodType)) {
            /* @var $request IRequestHelper */
            $request = Helper::getService(IRequestHelper::class);
            $methodType = constant(RequestMethod::class . '::' . strtoupper($methodType)) | ($request->isXmlHttpRequest() ? RequestMethod::XMLHTTPREQUEST : 0);
        }
        return $methodType;
    }

    /**
     * 
     * @param \PhSpring\Annotations\IExpression $expression
     * @param Clouser $checker
     * @return boolean
     */
    private static function expression(IExpression $expression, $checker) {
        if ($expression instanceof ExpressionAnd) {
            return self::expressionAnd($expression, $checker);
        } elseif ($expression instanceof ExpressionOr) {
            return self::expressionOr($expression, $checker);
        } elseif ($expression instanceof ExpressionNot) {
            return !$checker($expression->value);
        }
    }

    /**
     * 
     * @param \PhSpring\Annotations\ExpressionAnd $expression
     * @param Clouser $checker
     * @return boolean
     */
    private static function expressionAnd(ExpressionAnd $expression, $checker) {
        $ret = true;
        foreach ($expression->value as $val) {
            $ret &= $checker($val);
        }
        return !!$ret;
    }

    /**
     * 
     * @param \PhSpring\Annotations\ExpressionOr $expression
     * @param Clouser $checker
     * @return boolean
     */
    private static function expressionOr(ExpressionOr $expression, $checker) {
        $ret = false;
        foreach ($expression->value as $val) {
            $ret |= $checker($val);
        }
        return !!$ret;
    }

}
