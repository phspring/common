<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine;

use PhSpring\Engine\Adapter\Request;
use PhSpring\Engine\Adapter\RequestInterface;

/**
 * Description of HttpServletRequest
 *
 * @author lobiferi
 */
class HttpServletRequest implements RequestInterface{

    private static $adapter;

    /**
     * @return RequestInterface
     */
    private function getAdapter() {
        if (self::$adapter === null) {
            self::$adapter = new Request();
        }
        return self::$adapter;
    }

    public function getParam($key, $default = null) {
        return $this->getAdapter()->getParam($key, $default);
    }

    public function getParams() {
        return $this->getAdapter()->getParams();
    }

}
