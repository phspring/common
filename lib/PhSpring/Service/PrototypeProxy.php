<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Service;

use PhSpring\Engine\ClassInvoker;
use PhSpring\Service\AbstractServiceProxy;

/**
 * Description of PrototypeProxy
 *
 * @author lobiferi
 */
class PrototypeProxy extends AbstractServiceProxy {

    public function getInstance() {
        return ClassInvoker::getNewInstanceByRefl($this->getReflClass());
    }

}
