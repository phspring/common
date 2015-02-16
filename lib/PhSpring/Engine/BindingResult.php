<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine;

use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Description of BindingResult
 *
 * @author lobiferi
 */
class BindingResult extends ConstraintViolationList {

    /**
     *
     * @var array
     */
    private $map;

    /**
     * @todo Not recursive yet
     */
    public function toArray() {
        $ret = array();
        foreach ($this as $key => $value) {
            $ret[$this->map[get_class($value->getRoot())] . '.' . $value->getPropertyPath()] = $value->getMessage();
        }

        return $ret;
    }

}
