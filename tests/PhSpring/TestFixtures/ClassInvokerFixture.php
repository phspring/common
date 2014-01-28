<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\TestFixtures;

use PhSpring\TestFixtures\Singleton;
use PhSpring\Annotations\Autowired;
use PhSpring\Annotations\Qualifier;

/**
 * Description of CLassInvokerFixture
 *
 * @author lobiferi
 */
class ClassInvokerFixture {

    /**
     * @Autowired
     * @Qualifier("SingletonTestService")
     * @var Singleton
     */
    private $singleton;
    
    private $settedSingleton;
    /**
     * @var Singleton 
     */
    private $singleton2;

    /**
     * @Qualifier("SingletonTestService")
     */
    private $singleton3;

    /**
     * @var Singleton 
     * @Qualifier("SingletonTestService")
     */
    private $singleton4;

    /**
     * @var Autowired
     * @Qualifier("SingletonTestService")
     */
    private $singleton5;

    /**
     * @var integer
     */
    private $id;

    /**
     * @param int $id
     */
    public function __construct($id = null) {
        $this->id = $id;
    }

    /**
     * @param int $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @RequestParam(id)
     * @param int $id
     */
    public function setParamId($id) {
        $this->id = $id;
    }

    /**
     * @Autowired
     * @Qualifier("SingletonTestService")
     */
    public function setSingleton(Singleton $singleton) {
        $this->settedSingleton = $singleton;
    }

}
