<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine;

/**
 * Description of AbstractAdapter
 *
 * @author lobiferi
 */
abstract class AbstractAdapter {

    private $adapter;
    protected $defaultAdapter;

    /**
     * @return RequestInterface
     */
    protected function getAdapter() {
        if ($this->adapter === null && $this->defaultAdapter) {
            $clazz = $this->defaultAdapter;
            $this->adapter = new $clazz();
        }
        if ($this->adapter === null) {
            throw New \RuntimeException('Adapter not found! - Class: ' . $this->defaultAdapter);
        }
        return $this->adapter;
    }

    public static function setAdapter($adapter) {
        $interface = (new \ReflectionClass(__CLASS__))->getInterfaceNames()[0];
        if (!($adapter instanceof $interface)) {
            throw new \RuntimeException('Class mismatch');
        }
        $this->adapter = $adapter;
    }

    //put your code here
}
