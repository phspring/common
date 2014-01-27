<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\TestFixtures;

use PhSpring\Engine\IRequestHelper;

/**
 * Description of RequestHelperFixture
 *
 * @author lobiferi
 */
class RequestHelperFixture implements IRequestHelper{
    private $params = array();
    public function getParam($key) {
        if(array_key_exists($key, $this->params)){
            return $this->params[$key];
        }
        return null;
    }

    public function getParams() {
        return $this->params;
    }

    public function setParam($key, $value) {
        return $this->params[$key] = $value;
    }

//put your code here
}
