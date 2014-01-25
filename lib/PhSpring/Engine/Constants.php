<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine;

/**
 * Description of Constants
 *
 * @author lobiferi
 */
class Constants {

    public static $php_default_types = array('int', 'integer', 'bool', 'boolean', 'float', 'double', 'real', 'string', 'array', 'unset');
    public static $php_pseudo_types = array('mixed', 'number', 'callback', 'void');

    const CONTEXT_PARAM_CONTEXT = 'context';
    const CONTEXT_PARAM_ARGS = 'args';
    const CONTEXT_PARAM_METHOD = 'context';
    const CONTEXT_PARAM_CLASS = 'context';
    const CONTEXT_PARAM_REFL_CLASS = 'context';
    const CONTEXT_PARAM_REFL_METHOD = 'context';

}
