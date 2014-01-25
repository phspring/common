<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Annotation;

use ArrayAccess;
use Iterator;
use Reflector;

/**
 * Description of Collection
 *
 * @author lobiferi
 */
class Collection implements Iterator, ArrayAccess {

    private $annotations = array();

    /**
     *
     * @var Reflector
     */
    private $reflector;

    public function __construct($annotations, Reflector $context) {
        $this->reflector = $context;
        $this->annotations = $annotations;
    }

    public function has($annotationClass) {
        return array_key_exists($annotationClass, $this->annotations);
    }

    public function get($annotationClass) {
        if ($this->has($annotationClass)) {
            return $this->annotations[$annotationClass];
        }
        return null;
    }

    public function run($context) {
        if (!empty($this->annotations)) {
            foreach ($this->annotations as $annotation) {
                Helper::getAnnotationHandler($annotation)->run($this->reflector, $context);
            }
        }
    }

    public function current() {
        return current($this->annotations);
    }

    public function key() {
        return key($this->annotations);
    }

    public function next() {
        return next($this->annotations);
    }

    public function rewind() {
        reset($this->annotations);
    }

    public function valid() {
        return key($this->annotations) !== null;
    }

    public function offsetExists($offset) {
        return $this->has($offset);
    }

    public function offsetGet($offset) {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value) {
        $this->annotations[$offset] = $value;
    }

    public function offsetUnset($offset) {
        unset($this->annotations[$offset]);
    }
    
    public function copyToArray() {
        return (array)$this->annotations;
    }

}
