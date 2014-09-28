<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine;

use Doctrine\Common\Annotations\PhpParser;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Cache\Cache;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * Description of ExtendedCachedReader
 *
 * @author lobiferi
 */
class ExtendedCachedReader implements Reader {

    /**
     * @var string
     */
    private static $CACHE_SALT = '@[Annot]';

    /**
     * @var Reader
     */
    private $delegate;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var boolean
     */
    private $debug;

    /**
     * @var array
     */
    private $loadedAnnotations;

    /**
     * Constructor
     *
     * @param Reader $reader
     * @param Cache $cache
     * @param bool $debug
     */
    public function __construct(Reader $reader, Cache $cache, $debug = false) {
        $this->delegate = $reader;
        $this->cache = $cache;
        $this->debug = (Boolean) $debug;
    }

    /**
     * Get annotations for class
     *
     * @param \ReflectionClass $class
     * @return array
     */
    public function getClassAnnotations(\ReflectionClass $class) {
        $cacheKey = $class->getName();

        if (isset($this->loadedAnnotations[$cacheKey])) {
            return $this->loadedAnnotations[$cacheKey];
        }

        if (false === ($annots = $this->fetchFromCache($cacheKey, $class))) {
            $annots = $this->delegate->getClassAnnotations($class);
            $this->saveToCache($cacheKey, $annots);
        }

        return $this->loadedAnnotations[$cacheKey] = $annots;
    }

    /**
     * Get selected annotation for class
     *
     * @param \ReflectionClass $class
     * @param string $annotationName
     * @return null
     */
    public function getClassAnnotation(\ReflectionClass $class, $annotationName) {
        foreach ($this->getClassAnnotations($class) as $annot) {
            if ($annot instanceof $annotationName) {
                return $annot;
            }
        }

        return null;
    }

    /**
     * Get annotations for property
     *
     * @param ReflectionProperty $property
     * @return array
     */
    public function getPropertyAnnotations(ReflectionProperty $property) {
        $class = $property->getDeclaringClass();
        $cacheKey = $class->getName() . '$' . $property->getName();

        if (isset($this->loadedAnnotations[$cacheKey])) {
            return $this->loadedAnnotations[$cacheKey];
        }

        if (false === ($annots = $this->fetchFromCache($cacheKey, $class))) {
            $annots = $this->delegate->getPropertyAnnotations($property);
            $this->saveToCache($cacheKey, $annots);
        }

        return $this->loadedAnnotations[$cacheKey] = $annots;
    }

    /**
     * Get selected annotation for property
     *
     * @param ReflectionProperty $property
     * @param string $annotationName
     * @return null
     */
    public function getPropertyAnnotation(ReflectionProperty $property, $annotationName) {
        foreach ($this->getPropertyAnnotations($property) as $annot) {
            if ($annot instanceof $annotationName) {
                return $annot;
            }
        }

        return null;
    }

    /**
     * Get method annotations
     *
     * @param ReflectionMethod $method
     * @return array
     */
    public function getMethodAnnotations(ReflectionMethod $method) {
        $class = $method->getDeclaringClass();
        $cacheKey = $class->getName() . '#' . $method->getName();

        if (isset($this->loadedAnnotations[$cacheKey])) {
            return $this->loadedAnnotations[$cacheKey];
        }

        if (false === ($annots = $this->fetchFromCache($cacheKey, $class))) {
            $annots = $this->delegate->getMethodAnnotations($method);
            $this->saveToCache($cacheKey, $annots);
        }

        return $this->loadedAnnotations[$cacheKey] = $annots;
    }

    /**
     * Get selected method annotation
     *
     * @param ReflectionMethod $method
     * @param string $annotationName
     * @return null
     */
    public function getMethodAnnotation(ReflectionMethod $method, $annotationName) {
        foreach ($this->getMethodAnnotations($method) as $annot) {
            if ($annot instanceof $annotationName) {
                return $annot;
            }
        }

        return null;
    }

    /**
     * Clear loaded annotations
     */
    public function clearLoadedAnnotations() {
        $this->loadedAnnotations = array();
    }

    /**
     * Fetches a value from the cache.
     *
     * @param string           $rawCacheKey The cache key.
     * @param \ReflectionClass $class       The related class.
     * @return mixed|boolean The cached value or false when the value is not in cache.
     */
    private function fetchFromCache($rawCacheKey, \ReflectionClass $class) {
        $cacheKey = $rawCacheKey . self::$CACHE_SALT;
        if (($data = $this->cache->fetch($cacheKey)) !== false) {
            if (!$this->debug || $this->isCacheFresh($cacheKey, $class)) {
                return $data;
            }
        }

        return false;
    }

    /**
     * Saves a value to the cache
     *
     * @param string $rawCacheKey The cache key.
     * @param mixed  $value       The value.
     */
    private function saveToCache($rawCacheKey, $value) {
        $cacheKey = $rawCacheKey . self::$CACHE_SALT;
        $this->cache->save($cacheKey, $value);
        if ($this->debug) {
            $this->cache->save('[C]' . $cacheKey, time());
        }
    }

    /**
     * Check if cache is fresh
     *
     * @param string $cacheKey
     * @param \ReflectionClass $class
     * @return bool
     */
    private function isCacheFresh($cacheKey, \ReflectionClass $class) {
        if (false === $filename = $class->getFilename()) {
            return true;
        }

        return $this->cache->fetch('[C]' . $cacheKey) >= filemtime($filename);
    }

    public function getUseStatements(ReflectionClass $class) {
        $cacheKey = $class->getName() . '[@Use]';

        if (isset($this->loadedAnnotations[$cacheKey])) {
            return $this->loadedAnnotations[$cacheKey];
        }

        if (false === ($statements = $this->fetchFromCache($cacheKey, $class))) {
            if (get_class($class) === \ReflectionClass::class && method_exists('ReflectionClass', 'getUseStatements')) {
                $statements = $class->getUseStatements();
            } else {
                $statements = (new PhpParser)->parseClass((new ReflectionClass($class->getName())));
            }
            $this->saveToCache($cacheKey, $statements);
        }

        return $this->loadedAnnotations[$cacheKey] = $statements;
    }

}
