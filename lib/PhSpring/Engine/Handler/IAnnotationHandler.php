<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine\Handler;

/**
 * Description of IAnnotationHandler
 *
 * @author lobiferi
 */
interface IAnnotationHandler {

    /**
     * 
     * @param \Reflector $refl
     * @param object $context
     */
    public function run(\Reflector $refl, $context);
}
