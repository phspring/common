<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Config;

use ArrayAccess;
use ArrayObject;
use InvalidArgumentException;
use PhSpring\Engine\RecursiveArrayObject;

/**
 * Description of Adapter
 *
 * @author lobiferi
 */
class FileAdapter implements IConfig{

    /**
     * @var ArrayAccess
     */
    private static $resource;

    public function __construct($configFile = null) {
        if (self::$resource === null) {
            if (file_exists($configFile)) {
                switch (pathinfo($configFile, PATHINFO_EXTENSION)) {
                    case 'ini':
                        self::$resource = new RecursiveArrayObject(parse_ini_file($configFile, true, INI_SCANNER_RAW));
                        break;
                    case 'php':
                    case 'inc':
                        self::$resource = new RecursiveArrayObject(require $configFile);
                        break;
                    default:
                        throw new InvalidArgumentException('Not supported config file: ' . pathinfo($configFile, PATHINFO_EXTENSION));
                }
            } else {
                throw new InvalidArgumentException('The config file don\'t exist: ' . $configFile);
            }
        }
    }

    public function __get($name) {
        return self::$resource->{$name};
    }

    public static function setResource(ArrayAccess $resource) {
        self::$resource = $resource;
    }

}
