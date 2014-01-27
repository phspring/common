<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Annotation;

use ArrayObject;
use Reflector;

/**
 * Description of Collection
 *
 * @author lobiferi
 */
class Collection extends ArrayObject {

    /**
     *
     * @var Reflector
     */
    private $reflector;

    public function __construct(array $annotations, Reflector $reflector) {
        parent::__construct($annotations);
        $this->reflector = $reflector;
    }

    public function run($context) {
        foreach ($this as $annotation) {
            Helper::getAnnotationHandler($annotation)->run($this->reflector, $context);
        }
    }

}
