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
    private $helper;
    public function __construct() {
        $this->helper = InvokerConfig::getRequestHelper();
    }

    public function getParam($key, $default = null) {
        $value = $this->helper->getParam($key);
        if ($value !== null) {
            return $value;
        }
        return $default;
    }

    public function getParams() {
        return $this->helper->getParams();
    }

    public function getMethod() {
        return $this->helper->getMethod();
    }

    public function getServer($key = null, $default = null) {
        return $this->helper->getServer($key, $default);
    }

    /**
     * Was the request made by POST?
     *
     * @return boolean
     */
    public function isPost() {
        return $this->helper->isPost();
    }

    /**
     * Was the request made by GET?
     *
     * @return boolean
     */
    public function isGet() {
        return $this->helper->isGet();
    }

    /**
     * Was the request made by PUT?
     *
     * @return boolean
     */
    public function isPut() {
        return $this->helper->isPut();
    }

    /**
     * Was the request made by DELETE?
     *
     * @return boolean
     */
    public function isDelete() {
        return $this->helper->isDelete();
    }

    /**
     * Was the request made by HEAD?
     *
     * @return boolean
     */
    public function isHead() {
        return $this->helper->isHead();
    }

    /**
     * Was the request made by OPTIONS?
     *
     * @return boolean
     */
    public function isOptions() {
        return $this->helper->isOptions();
    }

    /**
     * Is the request a Javascript XMLHttpRequest?
     *
     * Should work with Prototype/Script.aculo.us, possibly others.
     *
     * @return boolean
     */
    public function isXmlHttpRequest() {
        return $this->helper->isXmlHttpRequest();
    }

    /**
     * Is https secure request
     *
     * @return boolean
     */
    public function isSecure() {
        return $this->helper->isSecure();
    }

    public function setParam($key, $value) {
        return $this->helper->setParam($key, $value);
    }

}
