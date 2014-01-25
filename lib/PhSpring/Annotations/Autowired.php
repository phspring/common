<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Annotations;

use PhSpring\Engine\AnnotationAbstract;

/**
 * Description of Autovired
 *
 * @author lobiferi
 * @Annotation
 * @Target({"METHOD","PROPERTY"})
 */
class Autowired extends AnnotationAbstract {
    public function __construct(array $values) {}
}
