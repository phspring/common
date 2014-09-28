<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Annotation;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\PhpParser;
use Doctrine\Common\Annotations\Reader;
use InvalidArgumentException;
use PhSpring\ApplicationTimer;
use PhSpring\Engine\ClassInvoker;
use PhSpring\Engine\Constants;
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
     * @var Reader
     */
    private static $helper;

    /**
     *
     * @var array
     */
    private static $annotationHandlerNamespaces = array('PhSpring\Engine\Handler');

    /**
     * @return Reader
     */
    private static function getHelper() {
        if (self::$helper === null) {
            self::$helper = new AnnotationReader();
        }
        return self::$helper;
    }

    public static function setHelper(Reader $helper) {
        self::$helper = $helper;
    }

    public static function getUseStatements(ReflectionClass $class) {
        if (method_exists(self::$helper, 'getUseStatements')) {
            return self::$helper->getUseStatements($class);
        } else {
            return (new PhpParser)->parseClass((new \ReflectionClass($class->getName())));
        }
    }

    public static function getPropertyType(ReflectionProperty $property) {
        if (empty($property->getDocComment())) {
            return null;
        }
        if (strpos($property->getDocComment(), '@var ') === false) {
            return null;
        }
        $typeName = preg_split('/\s/', substr($property->getDocComment(), strpos($property->getDocComment(), '@var ') + 5))[0];
        if (class_exists($typeName) || interface_exists($typeName)) {
            return $typeName;
        }
        $paths = self::getUseStatements($property->getDeclaringClass());
        if ($property->getDeclaringClass()->getNamespaceName()) {
            $paths[] = $property->getDeclaringClass()->getNamespaceName();
        }
        $paths[] = '';

        foreach ($paths as $alias => $path) {
            if (strtolower($alias) == strtolower($typeName)) {
                return $path;
            } else if (class_exists($path . '\\' . $typeName) || interface_exists($path . '\\' . $typeName)) {
                return $path . '\\' . $typeName;
            }
        }
        return Constants::normalizeType(strtolower($typeName));
    }

    public static function __callStatic($name, $arguments) {

        return self::getHelper()->{$name}($arguments[0]);
    }

    public static function hasAnnotation(Reflector $refl, $annotation, array $values = null) {
        return self::getAnnotation($refl, $annotation, $values) !== null;
    }

    public static function getAnnotation(Reflector $refl, $annotationType, array $values = null) {
        $type = self::getReflectionType($refl);
        return self::getHelper()->{"get{$type}Annotation"}($refl, $annotationType);//->getAnnotation($annotationType, $values);
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

    public static function addAnnotationHandlerNamespace($namespace) {
        array_unshift(self::$annotationHandlerNamespaces, $namespace);
    }

    public static function getAnnotationHandler($annotationType) {
        if (is_object($annotationType)) {
            $annotationType = get_class($annotationType);
        }
        $annotationType = substr($annotationType, strrpos($annotationType, '\\') + 1);
        $found = false;
        ApplicationTimer::start();
        foreach (self::$annotationHandlerNamespaces as $ns) {
            $handler = $ns . '\\' . $annotationType . 'Handler';
            if (class_exists($handler)) {
                $found = $handler;
                break;
            }
        }
        ApplicationTimer::stop();
        if ($found !== false) {
            return ClassInvoker::getNewInstance($found);
        } else {
            return null;
        }
    }

}
