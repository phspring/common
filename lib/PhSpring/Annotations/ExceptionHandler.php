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
 * Description of ExceptionHandler
 *
 * @author lobiferi
 * @Annotation
 */
class ExceptionHandler extends AnnotationAbstract {

    public $value;

    public function __construct(array $values) {
        parent::__construct($values);
        if (!class_exists($this->value)) {
            throw new UnexpectedValueException("Exception class is not available - {$this->value}", ErrorCode::EXCEPTIONHANDLER_EXCEPTION_CLASS_IS_NOT_AVAILABLE);
        }
    }

}
