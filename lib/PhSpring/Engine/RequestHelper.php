<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine;

/**
 * Description of RequestHelper
 *
 * @author lobiferi
 */
class RequestHelper implements IRequestHelper {

    private $params = array();

    public function getParams() {
        return $this->params + filter_input_array(INPUT_REQUEST);
    }

    public function getParam($key) {
        if (filter_has_var(INPUT_REQUEST, $key)) {
            return filter_input(INPUT_REQUEST, $key);
        } elseif (array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }
        return null;
    }

    public function setParam($key, $value) {
        $this->params[$key] = $value;
    }

}
