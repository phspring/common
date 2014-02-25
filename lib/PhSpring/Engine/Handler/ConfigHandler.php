<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine\Handler;

use PhSpring\Annotations\Config;
use PhSpring\Annotation\Helper;
use PhSpring\Engine\Constants;
use PhSpring\Engine\Handler\IAnnotationHandler;
use PhSpring\Annotations\Autowired;

/**
 * Description of ConfigHandler
 *
 * @author lobiferi
 */
class ConfigHandler implements IAnnotationHandler {
    
    /**
     * @var PhSpring\Config\IConfig
     * @Autowired
     */
    private $config;

    public function run(Reflector $refl, $context) {
        if ($refl instanceof ReflectionProperty) {
            $annotation = Helper::getAnnotation($refl, Config::class);
            $path = explode('.', $annotation->value);
            while (!empty($path)) {
                $config = $config->{array_shift($path)};
            }
            $type = Helper::getPropertyType($refl);
            $isPrimitiveType = (in_array($type, Constants::$php_default_types) || in_array($type, Constants::$php_pseudo_types));
            $refl->setAccessible(true);
            if ($type !== null && $isPrimitiveType) {
                if (gettype($config) === $type) {
                    $refl->setValue($context, $config);
                }elseif($type==='array' && method_exists($config, 'toArray')){
                    $refl->setValue($context, $config->toArray());
                }
            }else{
                $refl->setValue($context, $config);
            }
        }
    }

//put your code here
}
