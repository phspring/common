<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Annotations;

use PhSpring\Engine\ErrorCode;
use UnexpectedValueException;

/**
 * Description of RequestParam
 *
 * @author lobiferi
 * @Annotation
 * @Target("METHOD")
 */
class RequestParam extends Autowired {

    public $defaultValue = '****UNDEFINED****';
    /** @var boolean */
    public $required = true;
    public $value;
    
    public function __construct(array $values) {
        if(!array_key_exists('value', $values)){
            throw new UnexpectedValueException("The 'value' parameter is required!", ErrorCode::REQUESTPARAM_PARAMETER_IS_REQUIRED);
        }
        $this->value = (string)$values['value'];
        if(array_key_exists('required', $values)){
            $this->required = (bool)$values['required'];
        }
        if(array_key_exists('defaultValue', $values)){
            $this->defaultValue = (string)$values['defaultValue'];
            $this->required = false;
        }
    }

}
