<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine\Handler;

use PhSpring\Annotation\Helper;
use PhSpring\Service\Helper as ServiceHelper;
use PhSpring\Annotations\Qualifier;
use ReflectionProperty;
use Reflector;

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
            $serviceName = $this->getServiceName($refl);
            $service = ServiceHelper::getService($type, $serviceName);
            $refl->setAccessible(true);
            $refl->setValue($context, $service);
        }
    }

    /**
     * @param ReflectionProperty $refl
     * @return string
     */
    public function getServiceName(ReflectionProperty $refl) {
        if ($annotation = Helper::getAnnotation($refl, Qualifier::class)) {
            return $annotation->getName();
        }
        return null;
    }

}
