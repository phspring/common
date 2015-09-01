<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine;

use UnexpectedValueException;

/**
 * Description of AnnotationAbstract
 *
 * @author lobiferi
 */
class AnnotationAbstract {

    public function __construct(array $values) {
        if (array_key_exists('value', $values)) {
            $this->value = $values['value'];
        } else {
            throw new UnexpectedValueException('Annotation value is not found - ' . get_class($this),  ErrorCode::ANNOTATIONABSTRACT_VALUE_IS_NOT_FOUND);
        }
    }

}
