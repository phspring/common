<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Service;

use PhSpring\Annotations\Component;
use PhSpring\Reflection\ReflectionClass;

/**
 * Description of AbstractServiceProxy
 *
 * @author lobiferi
 */
abstract class AbstractServiceProxy {

    abstract public function getInstance();

    /**
     *
     * @var ReflectionClass
     */
    private $reflClass;

    public function __construct(ReflectionClass $reflClass) {
        $this->reflClass = $reflClass;
    }

    public function getReflClass() {
        return $this->reflClass;
    }
    protected function getName() {
        $serviceName = $this->reflClass->getName();
        $component = $this->reflClass->getAnnotation(Component::class);
        if ($component && $component->name) {
            $serviceName = $component->name;
        }
        return $serviceName;
    }

    /**
     * 
     * @param string $type Class or Interface name
     * @return boolean
     */
    public function isInstanceOf($type) {
        $reflType = new \ReflectionClass($type);
        if ($reflType->isInterface()) {
            return $this->reflClass->implementsInterface($type);
        } else {
            return $this->reflClass->isSubclassOf($type) || $this->reflClass->getName() === $type;
        }
    }

}
