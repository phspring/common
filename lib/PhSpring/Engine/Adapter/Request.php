<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine\Adapter;

/**
 * Description of Request
 *
 * @author lobiferi
 */
class Request implements RequestInterface {

    public function getParam($key, $default = null) {
        if (filter_has_var(INPUT_GET | INPUT_POST, $key)) {
            return filter_input(INPUT_GET | INPUT_POST, $key);
        }
        return $default;
    }

    public function getParams() {
        return filter_input_array(INPUT_GET | INPUT_POST);
    }

}
