<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Annotation;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Reflection\Psr0FindFile;
use Doctrine\Common\Reflection\StaticReflectionParser;
use ErrorException;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Reflector;

/**
 * Description of Hepler
 *
 * @author lobiferi
 * @method Collection getClassAnnotations
 * @method Collection getPropertyAnnotations
 * @method Collection getMethodAnnotations
 */
class Helper {

    /**
     * @var AnnotationReader
     */
    private static $helper;

    /**
     * @return AnnotationReader
     */
    private static function getHelper() {
        if (self::$helper === null) {
            self::$helper = new AnnotationReader();
        }
        return self::$helper;
    }

    public static function getPropertyType(ReflectionProperty $property) {
        if (empty($property->getDocComment())) {
            return null;
        }
        if (strpos($property->getDocComment(), '@var ') === false) {
            return null;
        }
        $typeName = preg_split('/\s/', substr($property->getDocComment(), strpos($property->getDocComment(), '@var ') + 5))[0];
        try {
            if (class_exists($typeName)) {
                return $typeName;
            }
        } catch (ErrorException $exc) {
            
        }
        $className = $property->getDeclaringClass()->getName();

        $dir = substr($property->getDeclaringClass()->getFileName(), 0, -strlen($className) - 4);
        $firstPart = preg_split('/[\\\\_]/', $className)[0];

        $staticReflectionParser = new StaticReflectionParser($className, new Psr0FindFile(array($firstPart => array($dir))), false);
        $paths = (array) $staticReflectionParser->getUseStatements();
        if ($staticReflectionParser->getNamespaceName()) {
            $paths[] = $staticReflectionParser->getNamespaceName();
        }
        $paths[] = '';

        foreach ($paths as $alias => $path) {
            if (strtolower($alias) == strtolower($typeName)) {
                return $path;
            }
            try {
                if (class_exists($path . '\\' . $typeName)) {
                    return $path . '\\' . $typeName;
                }
            } catch (ErrorException $exc) {
                
            }
        }
        return null;
    }

    public static function __callStatic($name, $arguments) {

        return new Collection(self::getHelper()->{$name}($arguments[0]), $arguments[0]);
    }

    public static function hasAnnotation(Reflector $refl, $annotation, array $values = null) {
        return self::getAnnotation($refl, $annotation, $values) !== null;
    }

    public static function getAnnotation(Reflector $refl, $annotationType, array $values = null) {
        $type = self::getReflectionType($refl);
        return self::{"get{$type}Annotations"}($refl)->getAnnotation($annotationType, $values);
    }

    public static function getAnnotations(Reflector $refl) {
        $type = self::getReflectionType($refl);
        return self::{"get{$type}Annotations"}($refl);
    }

    private static function getReflectionType(\Reflector $refl) {
        if ($refl instanceof ReflectionClass) {
            return 'Class';
        }
        if ($refl instanceof ReflectionMethod) {
            return 'Method';
        }
        if ($refl instanceof ReflectionProperty) {
            return 'Property';
        }
        throw new InvalidArgumentException("Not supported reflection type");
    }

}
