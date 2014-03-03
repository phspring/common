<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine;

use PhSpring\Annotations\RequestMethod;
use PhSpring\Engine\Adapter\Request;
use PhSpring\Engine\Adapter\RequestInterface;
use PhSpring\Engine\Exceptions\UnSupportedRequestException;

/**
 * Description of HttpServletRequest
 *
 * @author lobiferi
 */
class HttpServletRequest implements RequestInterface {

    const PATH_INFO = 'PATH_INFO';
    const HTTP_X_REQUESTED_WITH = 'HTTP_X_REQUESTED_WITH';
    const REQUEST_METHOD = 'REQUEST_METHOD';
    const HTTPS = 'HTTPS';

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

    public static function setAdapter(RequestInterface $adapter) {
        self::$adapter = $adapter;
    }

    public function getParam($key, $default = null) {
        return $this->getAdapter()->getParam($key, $default);
    }

    public function getParams() {
        return $this->getAdapter()->getParams();
    }

    public function getMethod() {
        return $this->getAdapter()->getMethod();
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
