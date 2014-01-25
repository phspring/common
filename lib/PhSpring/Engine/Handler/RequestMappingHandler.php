<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine\Handler;

use PhSpring\Annotations\ExpressionAnd;
use PhSpring\Annotations\ExpressionNot;
use PhSpring\Annotations\ExpressionOr;
use PhSpring\Annotations\Handler\IAnnotationHandler;
use PhSpring\Annotations\IExpression;
use PhSpring\Annotations\RequestMethod;
use PhSpring\Engine\ErrorCode;
use PhSpring\Engine\Exceptions\UnSupportedRequestException;
use Reflector;
use UnexpectedValueException;

/**
 * Description of RequestMappingHandler
 *
 * @author lobiferi
 */
class RequestMappingHandler implements IAnnotationHandler {
    
    private $annotation;

    public function __construct($annotation) {
        $this->annotation = $annotation;
    }

    public function run(Reflector $reflMethod, $context) {
        $constName = RequestMethod::class . '::' . filter_input(INPUT_SERVER, 'REQUEST_METHOD');
        if (!defined($constName)) {
            throw new UnSupportedRequestException("Unknown request method : {$constName}", ErrorCode::REQUESTMAPPINGHANDLER_UNKNOWN_REQUEST_METHOD);
        }
        $methodType = constant($constName) | (filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH') ? RequestMethod::XMLHTTPREQUEST : 0);
        $checker = function($value)use($methodType, &$checker){
            if(is_integer($value)){
                return !!($value & $methodType);
            }elseif ($value instanceof IExpression) {
                if($value instanceof ExpressionAnd){
                    $ret = true;
                    foreach ($value->value as $val) {
                        $ret &= $checker($val);
                    }
                    return !!$ret;
                }elseif($value instanceof ExpressionOr){
                    $ret = false;
                    foreach ($value->value as $val) {
                        $ret |= $checker($val);
                    }
                    return !!$ret;
                }elseif($value instanceof ExpressionNot){
                     return !$checker($value->value);
                }
            }
            throw new UnexpectedValueException("This annotation value is not supported: {$value}", ErrorCode::REQUESTMAPPINGHANDLER_VALUE_IS_NOT_SUPPORTED);
        };
        if (!$checker($this->annotation->method)) {
            throw new UnSupportedRequestException('The request is not mismatched', ErrorCode::REQUESTMAPPINGHANDLER_UNKNOWN_REQUEST_METHOD);
        }
    }

}
