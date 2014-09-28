<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Service;

use PhSpring\Engine\ClassInvoker;

/**
 * Description of LazyProxy
 *
 * @author lobiferi
 */
class LazyProxy extends AbstractServiceProxy {

    public function getInstance() {
        if ($this->getReflClass()->hasMethod('getInstance')) {
            $type = ($this->getReflClass()->getName());
            $service = $type::getInstance();
        } else {
            $service = ClassInvoker::getNewInstanceByRefl($this->getReflClass());
        }
        Collection::add($this->getName(), $service);
        return $service;
    }

}
