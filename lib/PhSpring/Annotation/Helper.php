<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Annotation;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Reflection\StaticReflectionParser;
use Doctrine\Common\Reflection\Psr0FindFile;

/**
 * Description of Hepler
 *
 * @author lobiferi
 */
class Helper extends AnnotationReader {

    /**
     *
     * @var PhSpring\Annotation\Hepler
     */
    private static $instance;

    /**
     * @return \PhSpring\Annotation\Hepler
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function getPropertyType(\ReflectionProperty $property) {
        $typeName = preg_split('/\s/', substr($property->getDocComment(), strpos($property->getDocComment(), '@var ') + 5))[0];
        try {
            if (class_exists($typeName)) {
                return $typeName;
            }
        } catch (\ErrorException $exc) {
            
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
            } catch (\ErrorException $exc) {
                
            }
        }
        return null;
    }

    /**
     * 
     * @param mixed $annotation
     * @return PhSpring\Annotations\Handler\IAnnotationHandler
     */
    public static function getAnnotationHandler($annotation) {
        if (is_object($annotation)) {
            $handler = preg_replace('/(.*)\\\\([A-Za-z]+)$/', '$1\\\\Handler\\\\$2', get_class($annotation));
        } else {
            $handler = preg_replace('/(.*)\\\\([A-Za-z]+)$/', '$1\\\\Handler\\\\$2', $annotation);
        }

        return new $handler($annotation);
    }

    public function getClassAnnotations(\ReflectionClass $class) {
        return new Collection(parent::getClassAnnotations($class), $class);
    }

    public function getPropertyAnnotations(\ReflectionProperty $property) {
        return new Collection(parent::getPropertyAnnotations($property), $property);
    }

    public function getMethodAnnotations(\ReflectionMethod $method) {
        return new Collection(parent::getMethodAnnotations($method), $method);
    }

    public static function hasAnnotation(\Reflector $refl, $annotation, array $values = null) {
        if ($refl instanceof \ReflectionClass) {
            return self::getInstance()->getClassAnnotation($refl, $annotation, $values) !== null;
        }
        if ($refl instanceof \ReflectionMethod) {
            return self::getInstance()->getMethodAnnotation($refl, $annotation, $values) !== null;
        }
        if ($refl instanceof \ReflectionProperty) {
            return self::getInstance()->getPropertyAnnotation($refl, $annotation, $values) !== null;
        }
        throw new \Exception("Unsupported type");
    }

    public function getClassAnnotation(\ReflectionClass $class, $annotationName, array $values = null) {
        return $this->annotationFilter($this->getClassAnnotations($class), $annotationName, $values);
    }

    public function getMethodAnnotation(\ReflectionMethod $method, $annotationName, array $values = null) {
        return $this->annotationFilter($this->getMethodAnnotations($method), $annotationName, $values);
    }

    public function getPropertyAnnotation(\ReflectionProperty $property, $annotationName, array $values = null) {
        return $this->annotationFilter($this->getPropertyAnnotations($property), $annotationName, $values);
    }

    private function annotationFilter($annotations, $annotationName, array $values = null) {
        foreach ($annotations as $annotation) {
            if ($annotation instanceof $annotationName) {
                if ($values !== null) {
                    $found = 1;
                    foreach ($values as $key => $value) {
                        $found &= $annotation->$key == $value;
                    }
                    if ($found) {
                        return $annotation;
                    }
                } else {
                    return $annotation;
                }
            }
        }

        return null;
    }

}
