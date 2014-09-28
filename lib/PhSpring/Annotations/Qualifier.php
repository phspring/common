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
 * Description of Qualifier
 *
 * @author lobiferi
 * @Annotation
 */
class Qualifier extends AnnotationAbstract {

    /**
     * @var string
     */
    public $value;

    public function __construct(array $values) {
        parent::__construct($values);
        if(!is_string($this->value)){
            throw new UnexpectedValueException("Unsupported annotation value", ErrorCode::QUALIFIER_UNSUPPORTED_ANNOTATION_VALUE);
        }
    }
}
