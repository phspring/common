<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine;

use ArrayObject;

/**
 * Description of RecursiveArrayObject
 *
 * @author lobiferi
 */
class RecursiveArrayObject extends ArrayObject {

    /**
     * overwrites the ArrayObject constructor for 
     * iteration through the "array". When the item
     * is an array, it creates another self() instead
     * of an array
     * 
     * @param Array $array data array
     */
    public function __construct(Array $array, $prop = ArrayObject::ARRAY_AS_PROPS) {
        $this->setFlags($prop);
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $value = new static($value);
            }
            $this->offsetSet($key, $value);
        }
    }
}