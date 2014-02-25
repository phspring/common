<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Annotations;

use Doctrine\Common\Annotations\Annotation\Required;
use PhSpring\Annotations\Autowired;
use PhSpring\Annotations\Scope;
use PhSpring\Reflection\ReflectionClass;

/**
 * Description of Component
 *
 * @author lobiferi
 * @Target("CLASS")
 * @Annotation

 */
class Component extends Autowired {

    public $scope = Scope::SINGLETON;
    public $name;

    public function __construct(array $values) {
        if (array_key_exists('value', $values)) {
            $this->name = $values['value'];
        }
        parent::__construct($values);
    }

}
