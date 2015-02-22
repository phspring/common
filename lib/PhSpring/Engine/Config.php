<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace PhSpring\Engine;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Proxy\Exception\InvalidArgumentException;

/**
 * Description of Config
 *
 * @author lobiferi
 */
class Config
{

    public static function init($dir = null)
    {
        if (! $dir) {
            throw new InvalidArgumentException('root directory specification is missing ( Config::init(__DIR__), or set the APPLICATION_PATH constant )');
        }
        $config = json_decode(file_get_contents($dir . 'vendor/bin/phspring/annotation.json'));
        foreach ($config as $ns => $path) {
            self::addAnnotationNamespace($ns, $path);
        }
    }

    public static function addAnnotationNamespace($ns, $path)
    {
        AnnotationRegistry::registerAutoloadNamespace($ns, $path);
    }
}
