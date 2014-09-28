<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Annotations;

use Doctrine\Common\Annotations\Annotation\Target;
use PhSpring\Engine\AnnotationAbstract;
/**
 * Description of Controller
 *
 * @author lobiferi
 * @Annotation
 * @Target(value="CLASS")
 */
class Controller extends Component{
    function __construct() {
        
    }
}
