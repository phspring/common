<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine\Exceptions;

use RuntimeException;

/**
 * Description of UnSupportedRequestException
 *
 * @author lobiferi
 */
class UnSupportedRequestException extends RuntimeException{
    public function __construct($message=null, $code=null, $previous=null) {
        parent::__construct('UnSupportedRequestException'.(empty($message)?'':' : ').$message, $code, $previous);
    }
}
