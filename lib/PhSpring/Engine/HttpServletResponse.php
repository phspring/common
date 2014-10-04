<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine;

use PhSpring\Annotations\Autowired;
use PhSpring\Engine\Adapter\Request;
use PhSpring\Engine\Adapter\Response;
use PhSpring\Engine\Adapter\ResponseInterface;

/**
 * Description of HttpServletResponse
 *
 * @author lobiferi
 */
class HttpServletResponse extends AbstractAdapter implements ResponseInterface {

    
    /**
     * @Autowired
     * @var Request
     */
    private $request;
    
    protected $defaultAdapter = Response::class;
    

    public function setResponse() {
        $adapter = $this->getAdapter();
        if (preg_match('/application\/json/', $this->request->getServer('HTTP_ACCEPT')) && $this->request->isXmlHttpRequest()) {
            $adapter->setResponse();
        }
    }

//put your code here
}
