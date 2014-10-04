<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine;

/**
 * Description of IResponseHelper
 *
 * @author lobiferi
 */
interface IResponseHelper {

    public function clearBody($name = null);

    public function setBody($content, $name = null);

    public function setHeader($name, $value, $replace = false);

    public function clearHeaders();

    public function sendResponse();

    public function setNoRender();
}
