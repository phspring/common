<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Annotations;

use Doctrine\Common\Annotations\Annotation\Target;
use PhSpring\Engine\AnnotationAbstract;

/**
 * Description of Scope
 *
 * @author lobiferi
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
class Scope extends AnnotationAbstract {

    const SINGLETON = 'singleton';
    const PROTOTYPE = 'prototype';
    const REQUEST = 'request';
    const SESSION = 'session';

    public function __construct(array $values) {
        if (!array_key_exists('value', $values)) {
            $values['values'] = self::SINGLETON;
        }
        parent::__construct($values);
    }

}
