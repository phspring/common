<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Generator;

/**
 * Description of Class
 *
 * @author lobiferi
 */
class Proxy extends \Zend_CodeGenerator_Php_Class {

    private $namespace;

    /**
     * 
     * @param string $namespace
     * @return \PhSpring\Generator\Proxy
     */
    public function setNamespace($namespace) {
        if (!empty($namespace)) {
            $this->namespace = 'namespace ' . $this->namespace . ';' . PHP_EOL;
        }
        return $this;
    }

    public function generate() {
        return $this->namespace . parent::generate();
    }

}
