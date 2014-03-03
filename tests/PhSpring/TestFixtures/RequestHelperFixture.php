<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\TestFixtures;

use PhSpring\Annotations\RequestMethod;
use PhSpring\Engine\IRequestHelper;

/**
 * Description of RequestHelperFixture
 *
 * @author lobiferi
 */
class RequestHelperFixture implements IRequestHelper {

    private $isDelete = false;
    private $isGet = true;
    private $isHead = false;
    private $isOptions = false;
    private $isPost = false;
    private $isPut = false;
    private $isSecure = false;
    private $isXmlHttpRequest = false;
    private $params = array();
    
    private $method = RequestMethod::GET;

    public function getParam($key) {
        if (array_key_exists($key, $this->params)) {
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

    public function getMethod() {
        return $this->method;
    }

    public function getServer($key = null, $default = null) {
        
    }

    public function isDelete() {
        return $this->isDelete;
    }

    public function isGet() {
        return $this->isGet;
    }

    public function isHead() {
        return $this->isHead;
    }

    public function isOptions() {
        return $this->isOptions;
    }

    public function isPost() {
        return $this->isPost;
    }

    public function isPut() {
        return $this->isPut;
    }

    public function isSecure($isSecure = null) {
        if ($isSecure !== null) {
            $this->isSecure = $isSecure;
        }
        return $this->isSecure;
    }

    public function isXmlHttpRequest() {
        return $this->isXmlHttpRequest;
    }

    public function setMethod($method) {
        $this->isDelete = !!($method & RequestMethod::DELET);
        $this->isGet = !!($method & RequestMethod::GET);
        $this->isHead = !!($method & RequestMethod::HEAD);
        $this->isOptions = !!($method & RequestMethod::OPTIONS);
        $this->isPost = !!($method & RequestMethod::POST);
        $this->isPut = !!($method & RequestMethod::PUT);
        $this->isXmlHttpRequest = !!($method & RequestMethod::XMLHTTPREQUEST);
        $this->method = $method;
    }

//put your code here
}
