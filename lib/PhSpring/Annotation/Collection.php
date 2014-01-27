<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Annotation;

use ArrayObject;
use PhSpring\Engine\AnnotationAbstract;
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
    /**
     * @param string $annotationType name of the annotation class
     * @return boolean
     */
    public function hasAnnotation($annotationType, array $values = null) {
        foreach ($this as $annotation) {
            if ($annotation instanceof $annotationType && $this->checkAnnotationByValue($annotation, $values)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * @param type $annotationType name of the annotation class
     * @param type $values extra annotation parameter to filter the result
     * @return null|AnnotationAbstract
     */
    public function getAnnotation($annotationType, array $values = null) {
        foreach ($this as $annotation) {
            if ($annotation instanceof $annotationType && $this->checkAnnotationByValue($annotation, $values)) {
                return $annotation;
            }
        }

        return null;
    }

    /**
     * 
     * @param AnnotationAbstract $annotation
     * @param array $values
     * @return boolean
     */
    private function checkAnnotationByValue(AnnotationAbstract $annotation, array $values = null) {
        $found = true;
        if ($values !== null) {
            foreach ($values as $key => $value) {
                $found &= $annotation->$key == $value;
            }
        }
        return !!$found;
    }
    
    

}
