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

    /**
     * 
     * @param string $serviceName
     * @return object Service instance 
     */
    static public function get($serviceName) {
        if (array_key_exists($serviceName, self::$services)) {
            return self::getByName($serviceName);
        } else {
            return self::getByType($serviceName);
        }
    }

    /**
     * 
     * @param string $serviceName
     * @return object Service instance
     * @throws NoSuchBeanDefinitionException
     */
    private static function getByType($serviceName) {
        $instances = array();
        foreach (self::$services as $service) {
            $instance = self::getInstance($service, $serviceName);
            if ($instance) {
                $instances[] = $instance;
            }
        }
        if (sizeof($instances) > 1) {
            throw new NoSuchBeanDefinitionException();
        } elseif (!empty($instances)) {
            return $instances[0];
        }
        return null;
    }

    private static function getByName($serviceName) {
        $service = self::$services[$serviceName];
        if ($service instanceof AbstractServiceProxy) {
            return $service->getInstance();
        } else {
            return $service;
        }
    }

    private static function getInstance($service, $serviceName) {
        if ($service instanceof AbstractServiceProxy) {
            if ($service->isInstanceOf($serviceName)) {
                return $service->getInstance();
            }
        } else if ($service instanceof $serviceName) {
            return $service;
        }
        return null;
    }

    static public function add($serviceName, $service) {
        self::$services[$serviceName] = $service;
    }

}
