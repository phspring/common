<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine\Handler;

use PhSpring\Annotations\Autowired;
use PhSpring\Engine\HttpServletResponse;
use Reflector;

/**
 * Description of ResponseBodyHandler
 *
 * @author lobiferi
 */
class ResponseBodyHandler implements IAnnotationHandler {
    /**
     * @var HttpServletResponse
     * @Autowired
     */
    private $response;
    
    public function run(Reflector $refl, $context) {
        $this->response->setResponse();
    }
}
