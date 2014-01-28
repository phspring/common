<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine;

/**
 * Description of Config
 *
 * @author lobiferi
 */
class Config {
    public static function init() {
        $path = str_replace(str_replace('\\', DIRECTORY_SEPARATOR, __NAMESPACE__), '', __DIR__);
        AnnotationRegistry::registerAutoloadNamespace("PhSpring\Annotations", $path);
    }
}
