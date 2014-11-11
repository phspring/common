<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine\Adapter;

use PhSpring\Engine\InvokerConfig;

/**
 * Description of Response
 *
 * @author lobiferi
 */
class Response implements ResponseInterface {

    private $helper;

    public function __construct() {
        $this->helper = InvokerConfig::getResponseHelper();
    }
    
    public function setResponse() {
            $this->helper->setHeader('Content-Type', 'application/json; charset=utf-8');
            $this->helper->setNoRender();
    }

//put your code here
}
