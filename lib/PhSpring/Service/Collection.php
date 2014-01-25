<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Service;

/**
 * Description of Collection
 *
 * @author lobiferi
 */
class Collection {

    static private $services = array();

    static public function get($serviceName) {
        try {
            if (array_key_exists($serviceName, self::$services)) {
                return self::$services[$serviceName];
            } else {
                foreach(self::$services as $service){
                    if($service instanceof $serviceName){
                        return $service;
                    }
                }
            }
        } catch (Exception $exc) {
            //do nothing
        }

        return null;
    }

    static public function add($serviceName, $service) {
        self::$services[$serviceName] = $service;
    }

}
