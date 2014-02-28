<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Description of BindingResult
 *
 * @author lobiferi
 */
class BindingResult implements IteratorAggregate, Countable, ArrayAccess {

    /**
     *
     * @var ConstraintViolationList
     */
    private $result;

    public function __toString() {
        return (string) $this->result;
    }

    public function count() {
        return $this->result->count();
    }

    public function getIterator() {
        return $this->result->getIterator();
    }

    public function offsetExists($offset) {
        return $this->result->offsetExists($offset);
    }

    public function offsetGet($offset) {
        return $this->result->offsetGet($offset);
    }

    public function offsetSet($offset, $violation) {
        $this->result->offsetSet($offset, $violation);
    }

    public function offsetUnset($offset) {
        $this->result->offsetUnset($offset);
    }

    public function setResult(ConstraintViolationList $result) {
        $this->result = $result;
    }

}
