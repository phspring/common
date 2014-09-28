<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\TestFixtures\Form;

use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;


/**
 * Description of SimpleForm
 *
 * @author lobiferi
 */
class SimpleForm {

    /**
     * @NotBlank
     */
    private $firstName;
    /**
     * @NotBlank
     */
    private $lastName;
    /**
     * @var array
     * @NotNull
     * @NotBlank
     */
    private $phones;
    
    /**
     * @Valid
     * @NotNull
     * @var Address
     */
    private $address;

}
