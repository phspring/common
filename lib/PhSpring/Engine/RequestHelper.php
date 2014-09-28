<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine;

use PhSpring\Annotations\RequestMethod;
use PhSpring\Engine\Exceptions\UnSupportedRequestException;

/**
 * Description of RequestHelper
 *
 * @author lobiferi
 */
class RequestHelper implements IRequestHelper {

    private $params = array();
    
    private $filters;
    
    function __construct() {
        $this->filters = filter_list();
    }

        public function getParams() {
        $ret = $this->params;
        $ret += (array) filter_input_array(INPUT_GET);
        $ret += (array) filter_input_array(INPUT_POST);
        return $ret;
    }

    public function getParam($key) {
        if (filter_has_var(INPUT_GET | INPUT_POST, $key)) {
            return filter_input(INPUT_GET | INPUT_POST, $key);
        } elseif (array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }
        return null;
    }

    public function setParam($key, $value) {
        $this->params[$key] = $value;
    }
    
    public function getMethod() {
        $constName = (RequestMethod::class . '::' . strtoupper($this->getServer(self::REQUEST_METHOD)));
        if (!defined($constName)) {
            throw new UnSupportedRequestException("Unknown request method: '{$constName}'", ErrorCode::REQUESTMAPPING_UNKNOWN_REQUEST_METHOD);
        }
        return constant($constName) | ($this->getServer(self::HTTP_X_REQUESTED_WITH) ? RequestMethod::XMLHTTPREQUEST : 0);
    }

    public function getServer($key = null, $default = null) {
        var_dump($this->filters);die();
        if (null === $key) {
            return filter_list(INPUT_SERVER) ? filter_input_array(INPUT_SERVER) : $this->params;
        }

        return filter_has_var(INPUT_SERVER, $key) ? filter_input(INPUT_SERVER, $key) : $default;
    }

    /**
     * Was the request made by POST?
     *
     * @return boolean
     */
    public function isPost() {
        return !!(RequestMethod::POST & $this->getMethod());
    }

    /**
     * Was the request made by GET?
     *
     * @return boolean
     */
    public function isGet() {
        return !!(RequestMethod::GET & $this->getMethod());
    }

    /**
     * Was the request made by PUT?
     *
     * @return boolean
     */
    public function isPut() {
        return !!(RequestMethod::PUT & $this->getMethod());
    }

    /**
     * Was the request made by DELETE?
     *
     * @return boolean
     */
    public function isDelete() {
        return !!(RequestMethod::DELETE & $this->getMethod());
    }

    /**
     * Was the request made by HEAD?
     *
     * @return boolean
     */
    public function isHead() {
        return !!(RequestMethod::HEAD & $this->getMethod());
    }

    /**
     * Was the request made by OPTIONS?
     *
     * @return boolean
     */
    public function isOptions() {
        return !!(RequestMethod::OPTIONS & $this->getMethod());
    }

    /**
     * Is the request a Javascript XMLHttpRequest?
     *
     * Should work with Prototype/Script.aculo.us, possibly others.
     *
     * @return boolean
     */
    public function isXmlHttpRequest() {
        return !!(RequestMethod::XMLHTTPREQUEST & $this->getMethod());
    }

    /**
     * Is https secure request
     *
     * @return boolean
     */
    public function isSecure() {
        return ($this->getServer(self::HTTPS) === 'on');
    }

}
