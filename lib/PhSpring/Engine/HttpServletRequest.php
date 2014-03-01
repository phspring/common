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
        //return ($this->getHeader('X_REQUESTED_WITH') == 'XMLHttpRequest');
        $constName = (RequestMethod::class . '::' . strtoupper($this->getServer(self::REQUEST_METHOD)));
        if (!defined($constName)) {
            throw new UnSupportedRequestException("Unknown request method: '{$constName}'", ErrorCode::REQUESTMAPPING_UNKNOWN_REQUEST_METHOD);
        }
        return constant($constName) | ($this->getServer(self::HTTP_X_REQUESTED_WITH) ? RequestMethod::XMLHTTPREQUEST : 0);
    }

    public function getServer($key = null, $default = null) {
        if (null === $key) {
            return filter_input_array(INPUT_SERVER);
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
