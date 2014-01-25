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
}
