<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Annotations;

use PhSpring\Annotations\Autowired;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Description of Config
 *
 * @author lobiferi
 * @Annotation
 * @Target("PROPERTY")
 */
class Config extends Autowired{
    public $value;
    public function __construct(array $values) {
        $this->value = $values['value'];
    }
}
