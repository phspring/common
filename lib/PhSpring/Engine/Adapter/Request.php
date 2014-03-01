<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine\Adapter;

use PhSpring\Engine\InvokerConfig;

/**
 * Description of Request
 *
 * @author lobiferi
 */
class Request implements RequestInterface {

    public function getParam($key, $default = null) {
        $value = InvokerConfig::getRequestHelper()->getParam($key);
        if($value !== null){
            return $value;
        }
        return $default;
    }

    public function getParams() {
        return InvokerConfig::getRequestHelper()->getParams();
    }

}
