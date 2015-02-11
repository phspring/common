<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine\Handler;

use PhSpring\Engine\ErrorCode;
use PhSpring\Engine\Exceptions\UnSupportedRequestException;
use PhSpring\Engine\RequestMappingHelper;
use Reflector;

/**
 * Description of RequestMappingHandler
 *
 * @author lobiferi
 */
class RequestMappingHandler implements IAnnotationHandler {

    private $annotation;

    public function __construct($annotation) {
        $this->annotation = $annotation;
    }

    public function run(Reflector $reflMethod, $context) {
        if (!RequestMappingHelper::isMatching($this->annotation)) {
            throw new UnSupportedRequestException('The request is not mismatched:'.PHP_EOL.$reflMethod->getName().PHP_EOL.print_r($this->annotation, true).PHP_EOL.\print_r($_SERVER, true), ErrorCode::REQUESTMAPPINGHANDLER_UNKNOWN_REQUEST_METHOD);
        }
    }

}
