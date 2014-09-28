<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine;

/**
 * Description of IACLResource
 *
 * @author lobiferi
 */
interface IACLResource {
    public function isAllowed($role = null, $resource = null, $privilege = null);
}
