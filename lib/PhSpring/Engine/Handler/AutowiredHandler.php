<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine\Handler;

use PhSpring\Annotation\Helper;
use PhSpring\Engine\Constants;
use PhSpring\Service\Helper as ServiceHelper;
use ReflectionProperty;
use Reflector;
use RuntimeException;

/**
 * Description of Autowired
 *
 * @author lobiferi
 */
class AutowiredHandler implements IAnnotationHandler {

    /**
     * 
     * @param Reflector $refl
     * @param object $context
     */
    public function run(Reflector $refl, $context) {
        if ($refl instanceof ReflectionProperty) {
            $type = Helper::getPropertyType($refl);
            $isPrimitiveType = (in_array($type, Constants::$php_default_types) || in_array($type, Constants::$php_pseudo_types));
            $serviceName = ServiceHelper::getServiceName($refl);
            if(($type === null ||  $isPrimitiveType) && $serviceName === null){
                throw new RuntimeException("Must set the property type by @var annotation or you must use @Quealifier annotation to define the service");
            }
            $service = ServiceHelper::getService($type, $serviceName);
            $refl->setAccessible(true);
            $refl->setValue($context, $service);
        }
    }

}
