<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Annotations\Handler;

use PhSpring\Annotation\Helper as AnnotationHelper;
use PhSpring\Annotations\Qualifier;
use Doctrine\Common\Reflection\StaticReflectionParser;
use Doctrine\Common\Reflection\Psr0FindFile;

use PhSpring\Service\Helper as ServiceHelper;

/**
 * Description of Autowired
 *
 * @author lobiferi
 */
class Autowired implements IAnnotationHandler {

    private $annotation;

    public function __construct($annotation) {
        $this->annotation = $annotation;
    }

    /**
     * 
     * @param \Reflector $refl
     * @param object $context
     */
    public function run(\Reflector $refl, $context) {
        if ($refl instanceof \ReflectionProperty) {
            $type = AnnotationHelper::getPropertyType($refl);
            $serviceName = $this->getServiceName($refl);
            $service = ServiceHelper::getService($type, $serviceName);
            $refl->setAccessible(true);
            $refl->setValue($context, $service);
        }
    }

    /**
     * @param \ReflectionProperty $refl
     * @return string
     */
    public function getServiceName(\ReflectionProperty $refl) {
        if($annotation = AnnotationHelper::getInstance()->getPropertyAnnotation($refl, Qualifier::class)) {
            return $annotation->getName();
        }
        return null;
    }

}
