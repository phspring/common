<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\TestFixtures;

use PhSpring\TestFixtures\Singleton;
use PhSpring\Annotations\Autowired;


/**
 * Description of CLassInvokerFixture
 *
 * @author lobiferi
 */
class ClassInvokerFixture {
    
    /**
     * @Autowired
     * @var Singleton
     */
    private $singleton;

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
}
