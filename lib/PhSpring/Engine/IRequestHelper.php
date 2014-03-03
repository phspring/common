<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine;

/**
 * Description of IRequestHelper
 *
 * @author lobiferi
 */
interface IRequestHelper {

    public function getParams();

    /*
     * @param string $key request parameter name
     */

    public function getParam($key);

    /**
     * @param string $key request parameter name
     * @param mixed $value parameter value
     */
    public function setParam($key, $value);

    public function getMethod();

    public function getServer($key = null, $default = null);

    /**
     * Was the request made by POST?
     *
     * @return boolean
     */
    public function isPost();

    /**
     * Was the request made by GET?
     *
     * @return boolean
     */
    public function isGet();

    /**
     * Was the request made by PUT?
     *
     * @return boolean
     */
    public function isPut();

    /**
     * Was the request made by DELETE?
     *
     * @return boolean
     */
    public function isDelete();

    /**
     * Was the request made by HEAD?
     *
     * @return boolean
     */
    public function isHead();

    /**
     * Was the request made by OPTIONS?
     *
     * @return boolean
     */
    public function isOptions();

    /**
     * Is the request a Javascript XMLHttpRequest?
     *
     * Should work with Prototype/Script.aculo.us, possibly others.
     *
     * @return boolean
     */
    public function isXmlHttpRequest();

    /**
     * Is https secure request
     *
     * @return boolean
     */
    public function isSecure();
}
