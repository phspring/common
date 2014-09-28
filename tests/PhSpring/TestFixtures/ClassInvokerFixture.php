<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\TestFixtures;

use PhSpring\Annotations\Autowired;
use PhSpring\Annotations\Controller;
use PhSpring\Annotations\Qualifier;
use PhSpring\Annotations\Valid;
use PhSpring\Engine\BindingResult;
use PhSpring\TestFixtures\Form\SimpleForm;

/**
 * Description of CLassInvokerFixture
 *
 * @author lobiferi
 * @Controller
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
     * @var array
     */
    private $array;

    /**
     * @var int
     */
    private $int;
    /**
     * @var integer
     */
    private $id;
    private $bindingResult;
    private $form;

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

    /**
     * @Valid("form")
     */
    public function indexAction(SimpleForm $form, BindingResult $result) {
        $this->bindingResult = $result;
        $this->form = $form;
    }

}
