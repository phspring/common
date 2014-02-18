<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine\Adapter;

/**
 *
 * @author lobiferi
 */
interface RequestInterface {

    /**
     * Get an action parameter
     *
     * @param string $key
     * @param mixed $default Default value to use if key not found
     * @return mixed
     */
    public function getParam($key, $default = null);

    /**
     * Get all action parameters
     *
     * @return array
     */
    public function getParams();
}
