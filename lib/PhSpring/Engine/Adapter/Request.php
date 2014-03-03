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
        if ($value !== null) {
            return $value;
        }
        return $default;
    }

    public function getParams() {
        return InvokerConfig::getRequestHelper()->getParams();
    }

    public function getMethod() {
        return InvokerConfig::getRequestHelper()->getMethod();
    }

    public function getServer($key = null, $default = null) {
        return InvokerConfig::getRequestHelper()->getServer($key, $default);
    }

    /**
     * Was the request made by POST?
     *
     * @return boolean
     */
    public function isPost() {
        return InvokerConfig::getRequestHelper()->isPost();
    }

    /**
     * Was the request made by GET?
     *
     * @return boolean
     */
    public function isGet() {
        return InvokerConfig::getRequestHelper()->isGet();
    }

    /**
     * Was the request made by PUT?
     *
     * @return boolean
     */
    public function isPut() {
        return InvokerConfig::getRequestHelper()->isPut();
    }

    /**
     * Was the request made by DELETE?
     *
     * @return boolean
     */
    public function isDelete() {
        return InvokerConfig::getRequestHelper()->isDelete();
    }

    /**
     * Was the request made by HEAD?
     *
     * @return boolean
     */
    public function isHead() {
        return InvokerConfig::getRequestHelper()->isHead();
    }

    /**
     * Was the request made by OPTIONS?
     *
     * @return boolean
     */
    public function isOptions() {
        return InvokerConfig::getRequestHelper()->isOptions();
    }

    /**
     * Is the request a Javascript XMLHttpRequest?
     *
     * Should work with Prototype/Script.aculo.us, possibly others.
     *
     * @return boolean
     */
    public function isXmlHttpRequest() {
        return InvokerConfig::getRequestHelper()->isXmlHttpRequest();
    }

    /**
     * Is https secure request
     *
     * @return boolean
     */
    public function isSecure() {
        return InvokerConfig::getRequestHelper()->isSecure();
    }

    public function setParam($key, $value) {
        return InvokerConfig::getRequestHelper()->setParam($key, $value);
    }

}
