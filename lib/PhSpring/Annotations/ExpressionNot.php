<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Annotations;

use PhSpring\Engine\AnnotationAbstract;
use PhSpring\Engine\ErrorCode;
use UnexpectedValueException;

/**
 * Description of ExpressionNot
 *
 * @author lobiferi
 * @Annotation
 */
class ExpressionNot extends AnnotationAbstract implements IExpression{
    public $value;
    public function __construct(array $values) {
        parent::__construct($values);
        if( !(is_int($this->value)||$this->value instanceof IExpression)){
            throw new UnexpectedValueException("Annotation value is not supported", ErrorCode::EXPRESSIONNOT_VALUE_IS_NOT_SUPPORTED);
        }
    }
}
