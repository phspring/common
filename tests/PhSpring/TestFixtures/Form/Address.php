<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\TestFixtures\Form;

use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Description of Address
 *
 * @author lobiferi
 */
class Address {
    /**
     *
     * @NotBlank
     */
    private $street;
}
