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
class HttpServletRequest extends AbstractAdapter implements RequestInterface {

    const PATH_INFO = 'PATH_INFO';
    const HTTP_X_REQUESTED_WITH = 'HTTP_X_REQUESTED_WITH';
    const REQUEST_METHOD = 'REQUEST_METHOD';
    const HTTPS = 'HTTPS';
    const REQUEST_URI = 'REQUEST_URI';

    protected $defaultAdapter = Request::class;

    public function getParam($key, $default = null) {
        return $this->getAdapter()->getParam($key, $default);
    }

    public function getParams() {
        return $this->getAdapter()->getParams();
    }

    public function getMethod() {
        $method = $this->getAdapter()->getMethod();
        if ($method == 'POST' && empty(filter_input_array(INPUT_POST)) && $this->getAdapter()->getServer('CONTENT_LENGTH')) {
            $method = 'PUT';
        }
        return $method;
    }

    public function getServer($key = null, $default = null) {
        return $this->getAdapter()->getServer($key, $default);
    }

    public function isDelete() {
        return $this->getAdapter()->isDelete();
    }

    public function isGet() {
        return $this->getAdapter()->isGet();
    }

    public function isHead() {
        return $this->getAdapter()->isHead();
    }

    public function isOptions() {
        return $this->getAdapter()->isOptions();
    }

    public function isPost() {
        return $this->getAdapter()->isPost();
    }

    public function isPut() {
        return $this->getAdapter()->isPut();
    }

    public function isSecure() {
        return $this->getAdapter()->isSecure();
    }

    public function isXmlHttpRequest() {
        return $this->getAdapter()->isXmlHttpRequest();
    }

    public function setParam($key, $value) {
        return $this->getAdapter()->setParam($key, $value);
    }

}
