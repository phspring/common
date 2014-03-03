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

    public static function isMatching(RequestMapping $annotation) {
        $methodType = Helper::getService(HttpServletRequest::class)->getMethod();
        if (!is_int($methodType)) {
            /* @var $request IRequestHelper */
            $request = Helper::getService(IRequestHelper::class);
            $methodType = constant(RequestMethod::class . '::' . strtoupper($methodType)) | ($request->isXmlHttpRequest() ? RequestMethod::XMLHTTPREQUEST : 0);
        }

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

    public static function getMatchingMethod($controller) {
        $class = new ReflectionClass($controller);
        $classAnnotation = $class->getAnnotation(RequestMapping::class);
        /* @var $reflMethod ReflectionMethod */
        foreach ($class->getMethods(PHP_ReflectionMethod::IS_PUBLIC) as $reflMethod) {
            /* @var $methodAnnotation RequestMapping */
            $methodAnnotation = $reflMethod->getAnnotation(RequestMapping::class);
            /* @var $requestHelper HttpServletRequest */
            $requestHelper = Helper::getService(HttpServletRequest::class);
            $url = '';
            if ($classAnnotation) {
                $url .= $classAnnotation->value;
            }
            if ($methodAnnotation) {
                $url .= $methodAnnotation->value;
            }
            $regexp = '/^' . str_replace('/', '\\/', $url) . '/';
            if ($url && $methodAnnotation && $classAnnotation && preg_match($regexp, $requestHelper->getServer(HttpServletRequest::PATH_INFO)) && self::isMatching($methodAnnotation)) {
                return $reflMethod->getName();
            }
        }
        return null;
    }

    private static function expression(IExpression $expression, $checker) {
        if ($expression instanceof ExpressionAnd) {
            return self::expressionAnd($expression, $checker);
        } elseif ($expression instanceof ExpressionOr) {
            return self::expressionOr($expression, $checker);
        } elseif ($expression instanceof ExpressionNot) {
            return !$checker($expression->value);
        }
    }

    private static function expressionAnd(ExpressionAnd $expression, $checker) {
        $ret = true;
        foreach ($expression->value as $val) {
            $ret &= $checker($val);
        }
        return !!$ret;
    }

    private static function expressionOr(ExpressionOr $expression, $checker) {
        $ret = false;
        foreach ($expression->value as $val) {
            $ret |= $checker($val);
        }
        return !!$ret;
    }

}
