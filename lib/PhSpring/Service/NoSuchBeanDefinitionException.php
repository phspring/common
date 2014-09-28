<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Service;

/**
 * Description of NoSuchBeanDefinitionException
 *
 * @author lobiferi
 */
class NoSuchBeanDefinitionException extends \RuntimeException {

    public function __construct($name = null, $multiple = array()) {
        if (!empty($multiple)) {
            $message = sprintf('No unique bean of type \'%s\' is defined: expected single matching bean but found %d: [%s]', $name, count($multiple), implode(', ', $multiple));
        } else {
            $message = "No bean named '$name' is defined";
        }
        parent::__construct($message);
    }

}
