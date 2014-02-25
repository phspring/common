<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\TestFixtures\Service;


use PhSpring\Annotations\Lazy;
use PhSpring\Annotations\Component;

/**
 * Description of LazyComponent
 *
 * @author lobiferi
 * @Component("lazyComponent")
 * @Lazy
 */
class LazyComponent {
    public function __construct() {
        $GLOBALS['LazyComponent'] = true;
    }
}
