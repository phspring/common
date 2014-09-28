<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\TestFixtures;

/**
 * Description of Singleton
 *
 * @author lobiferi
 */
class Singleton {
   private static $instance; //put your code here
   static public function getInstance() {
       if(self::$instance === null){
           self::$instance = new self();
       }
       return self::$instance;
   }
   
   private function __construct() {
       
   }
}
