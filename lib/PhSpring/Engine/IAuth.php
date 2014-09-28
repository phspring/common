<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine;

/**
 * Description of IAuth
 *
 * @author lobiferi
 */
interface IAuth {
    public function getUserId();
    public function getUserRole();
    public function isAuthenticated();
    
}
