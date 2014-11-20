<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Annotations;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Target;
use InvalidArgumentException;
use PhSpring\Engine\AnnotationAbstract;

/**
 * Description of SessionAttributes
 *
 * @author lobiferi
 * @Annotation
 * @Target("CLASS")
 */
class SessionAttributes extends AnnotationAbstract{
    public $name;
    public $type;

    public function __construct(array $values) {
        if (!array_key_exists('value', $values)) {
            throw new InvalidArgumentException();
        }
        $this->name = $values['value'];
        if (array_key_exists('type', $values)) {
            if (!class_exists($values['type'])) {
                throw new InvalidArgumentException();
            }
            $this->type = $values['type'];
        }
    }

}
