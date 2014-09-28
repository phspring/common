<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Annotations;

use PhSpring\Engine\AnnotationAbstract;
use PhSpring\Engine\ErrorCode;
use PhSpring\Engine\Exceptions\UnSupportedRequestException;
use UnexpectedValueException;
/**
 * Description of RequestMapping
 *
 * @author lobiferi
 * @Annotation
 */
class RequestMapping extends AnnotationAbstract {

    public $value;
    public $method = RequestMethod::ALL;
    public $params;

    public function __construct(array $values) {
        if (array_key_exists('method', $values)) {
            $this->setMethod($values['method']);
        }
        if (array_key_exists('value', $values)) {
            if (is_integer($values['value'])) {
                $this->setMethod($values['value']);
            } elseif ($values['value'] instanceof IExpression) {
                $this->setMethod($values['value']);
            } else {
                $this->value = $values['value'];
            }
        }
        if (array_key_exists('params', $values)) {
            throw new UnexpectedValueException('The \'param\' parameter is not supported yet!', ErrorCode::REQUESTMAPPING_PARAMETER_IS_NOT_SUPPORTED_YET);
        }
    }

    private function setMethod($value) {
        if (is_string($value)) {
            $constName = RequestMethod::class . '::' . strtoupper($value);
            if (!defined($constName)) {
                throw new UnSupportedRequestException("Unknown request method: '{$constName}'", ErrorCode::REQUESTMAPPING_UNKNOWN_REQUEST_METHOD);
            }
            $this->method = constant($constName);
        } elseif (is_integer($value)) {
            if (!RequestMethod::valid($value)) {
                throw new UnSupportedRequestException("Unknown request method", ErrorCode::REQUESTMAPPING_UNKNOWN_REQUEST_METHOD);
            }
            $this->method = $value;
        }elseif($value instanceof IExpression){
            $this->method = $value;
        }
    }

}
