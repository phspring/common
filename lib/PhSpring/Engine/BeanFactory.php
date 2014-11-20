<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine;

use Doctrine\Tests\Common\Annotations\Fixtures\ClassWithInvalidAnnotationTargetAtClass;
use PhSpring\Annotations\Component;
use PhSpring\Annotations\Lazy;
use PhSpring\Annotations\Qualifier;
use PhSpring\Annotations\Scope;
use PhSpring\Reflection\ReflectionClass;
use PhSpring\Reflection\ReflectionProperty;
use PhSpring\Service\AbstractServiceProxy;
use PhSpring\Service\LazyProxy;
use PhSpring\Service\NoSuchBeanDefinitionException;
use PhSpring\Service\PrototypeProxy;
use Symfony\Component\Translation\Tests\String;
use Symfony\Component\Validator\Exception\RuntimeException;

/**
 * Description of BeanFactory
 *
 * @author lobiferi
 */
class BeanFactory {

    private static $autoLoadSupport = false;

    /**
     *
     * @var BeanFactory
     */
    private static $instance;
    private $beans = array();
    private $aliases = array();

    private function __construct() {
        
    }

    /**
     * 
     * @return BeanFactory
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Does this bean factory contain a bean with the given name? 
     * More specifically, is getBean(java.lang.String) able to obtain a bean instance for the given name?
     * Translates aliases back to the corresponding canonical bean name. 
     * Will ask the parent factory if the bean cannot be found in this factory instance.
     * @param string $beanName  the name of the bean to query
     * @return boolean whether a bean with the given name is defined
     */
    public function containsBean($beanName) {
        return array_key_exists($beanName, $this->beans);
    }

    /**
     * Return the aliases for the given bean name, if any. All of those aliases point to the same bean when used in a getBean(string) call.
     * If the given name is an alias, the corresponding original bean name and other aliases (if any) will be returned, with the original bean name being the first element in the array.
     * Will ask the parent factory if the bean cannot be found in this factory instance.
     * @return array(string)
     */
    public function getAliases($name) {
        $beanName = $this->formatBeanName($name);
        if (array_key_exists($beanName, $this->aliases)) {
            return $this->aliases[$beanName];
        }
        return array();
    }

    private function formatBeanName($name) {
        return str_replace(array('\\', '_'), '.', strtolower($name));
    }

    /**
     * Return an instance, which may be shared or independent, of the specified bean.
     * @param string the name of the bean to query
     * @param mixed $filter
     * @return object
     * @throws NoSuchBeanDefinitionException - if there's no such bean definition
     * @throws BeanDefinitionStoreException - if arguments have been given but the affected bean isn't a prototype
     * @throws BeansException - if the bean could not be created
     */
    public function getBean($name, $filter = null, $autoload = true) {
        $bean = $this->getBeanObject($name, $filter);

        if ($bean === null && $autoload && class_exists($name) && !interface_exists($name)) {
            $this->addBeanClass($name);
            $bean = $this->getBean($name, $filter, false);
        }

        if ($bean !== null) {
            if ($bean instanceof AbstractServiceProxy) {
                $proyedBean = $bean->getInstance();
                return $proyedBean;
            } else {
                return $bean;
            }
        }
        //var_dump($name, $bean === null, self::$autoLoadSupport, class_exists($name), !interface_exists($name));
        throw new NoSuchBeanDefinitionException($name);
    }

    private function getAlias($name) {
        $beanName = $this->formatBeanName($name);
        $aliases = $this->getAliases($beanName);
        if (count($aliases) === 1) {
            return $aliases[0];
        } else if (count($aliases) > 1) {
            throw new NoSuchBeanDefinitionException($name, $aliases);
        }
        return null;
    }

    private function getBeanObject($name, $filter = null) {
        $beanName = $this->formatBeanName($name);
        $bean = null;
        //echo PHP_EOL . __METHOD__ . ': ' . $beanName . PHP_EOL;
        if (array_key_exists($beanName, $this->beans)) {
            $bean = $this->beans[$beanName];
        } else {
            if ($this->getAlias($beanName)) {
                $bean = $this->beans[$this->getAlias($beanName)];
            }
        }
        switch (true) {
            case is_string($filter):
                if (!($bean instanceof $filter)) {
                    $bean = null;
                }
                break;
            case is_array($filter):
                throw new RuntimeException('Unimplemented feature');
        }
        return $bean;
    }

    /**
     * Determine the type of the bean with the given name.
     * @return Class
     * @throws NoSuchBeanDefinitionException - if there is no bean with the given name
     */
    public function getType(String $name) {
        return get_class($this->getBean($name));
    }

    /**
     * Is this bean a prototype?
     * @return boolean
     */
    public function isPrototype($name) {
        return $this->getBean($name) instanceof PrototypeProxy;
    }

    /**
     * Is this bean a shared singleton?
     * @return boolean 
     */
    public function isSingleton($name) {
        return !$this->isPrototype($name);
    }

    /**
     * Check whether the bean with the given name matches the specified type.
     * @param string $name the name of the bean to query
     * @param string $targetType the type to match against
     * @throws NoSuchBeanDefinitionException - if there is no bean with the given name
     * @return boolean
     */
    public function isTypeMatch($name, $targetType) {
        return $this->getBean($name) instanceof $targetType;
    }

    public function createBean($bean) {
        switch (true) {
            case is_string($bean) && class_exists($bean):
                if (method_exists($bean, 'getInstance')) {
                    $this->add($bean, $bean::getInstance());
                } else {
                    $this->add($bean, ClassInvoker::getNewInstance($bean));
                }
                break;
            default:
                break;
        }
    }

    /**
     * 
     * @param string $beanName
     * @return object Service instance 
     */
    public function get($beanName) {
        if (array_key_exists($beanName, $this->beans)) {
            return $this->getByName($beanName);
        } else {
            return $this->getByType($beanName);
        }
    }

    /**
     * 
     * @param string $beanName
     * @return object Service instance
     * @throws NoSuchBeanDefinitionException
     */
    private function getByType($beanName) {
        $instances = array();
        foreach ($this->beans as $bean) {
            $instance = $this->getInstance($bean, $beanName);
            if ($instance) {
                $instances[] = $instance;
            }
        }
        if (sizeof($instances) > 1) {
            throw new NoSuchBeanDefinitionException($beanName);
        } elseif (!empty($instances)) {
            return $instances[0];
        }
        return null;
    }

    private function getByName($beanName) {
        $bean = $this->beans[$beanName];
        if ($bean instanceof AbstractServiceProxy) {
            return $bean->getBeanInstance();
        } else {
            return $bean;
        }
    }

    private function getBeanInstance($bean, $beanName) {
        if ($bean instanceof AbstractServiceProxy) {
            if ($bean->isInstanceOf($beanName)) {
                return $bean->getBeanInstance();
            }
        } else if ($bean instanceof $beanName) {
            return $bean;
        }
        return null;
    }

    private function add($beanName, $bean) {
        $bName = $this->formatBeanName($beanName);
        $this->beans[$bName] = $bean;
        $this->addAliases($bean, $bName);
    }

    private function addAliases($bean, $beanName) {
        if ($bean instanceof AbstractServiceProxy) {
            $refl = $bean->getReflClass();
        } else {
            $refl = new ReflectionClass($bean);
        }
        $this->addAlias($beanName, $refl->getName());
        foreach ($refl->getInterfaces() as $interface) {
            $this->addAlias($beanName, $interface->getName());
        }
        while ($refl = $refl->getParentClass()) {
            $this->addAlias($beanName, $refl->getName());
        }
    }

    private function addAlias($beanName, $alias) {
        $a = $this->formatBeanName($alias);
        if (!isset($this->aliases[$a])) {
            $this->aliases[$a] = array();
        }
        $this->aliases[$a][] = $beanName;
    }

    private function addNewServiceInstance($type, $name = null) {
        $reflClass = new ReflectionClass($type);
        $lazy = $reflClass->hasAnnotation(Lazy::class);
        $scope = $this->getScope($reflClass);

        if ($scope === Scope::SINGLETON) {
            if ($lazy) {
                $this->addLazyInitService($reflClass, $name);
            } else {
                $this->addSingleton($reflClass, $name);
            }
        } else {
            $this->addPrototypeService($reflClass, $name);
        }
    }

    private function addPrototypeService(ReflectionClass $reflClass, $name = null) {
        $this->add($this->getName($reflClass, $name), new PrototypeProxy($reflClass));
    }

    private function addLazyInitService(ReflectionClass $reflClass, $name = null) {
        $this->add($this->getName($reflClass, $name), new LazyProxy($reflClass));
    }

    private function addSingleton(ReflectionClass $reflClass, $name = null) {
        if ($reflClass->hasMethod('getInstance')) {
            $type = $reflClass->getName();
            $service = $type::getInstance();
        } else {
            $service = ClassInvoker::getNewInstanceByRefl($reflClass);
        }
        $this->add($this->getName($reflClass, $name), $service);
    }

    public function getName(ReflectionClass $reflClass, $name = null) {
        if ($name !== null) {
            return $name;
        }
        $serviceName = $reflClass->getName();
        $component = $reflClass->getAnnotation(Component::class);
        if ($component && $component->name) {
            $serviceName = $component->name;
        }
        return $serviceName;
    }

    private function getScope(ReflectionClass $reflClass) {
        $scope = Scope::SINGLETON;
        if ($reflClass->hasAnnotation(Component::class)) {
            $annotation = $reflClass->getAnnotation(Component::class);
            $scope = $annotation->scope;

            if ($reflClass->hasAnnotation(Scope::class)) {
                $annotation = $reflClass->getAnnotation(Scope::class);
                $scope = $annotation->value;
            }
        }
        /**
         * @todo Need more work
         */
        switch ($scope) {
            case Scope::SINGLETON:
            case Scope::REQUEST:
            case Scope::SESSION:
                $scope = Scope::SINGLETON;
                break;
            default:
                $scope = Scope::PROTOTYPE;
                break;
        }
        return $scope;
    }

    /**
     * @param ReflectionProperty $refl
     * @return string
     */
    public function getServiceName(ReflectionProperty $refl) {
        $annotation = AnnotationHelper::getAnnotation($refl, Qualifier::class);
        if ($annotation) {
            return $annotation->value;
        }
        return null;
    }

    public function addBeanClass($className, $name = null) {
        $this->addNewServiceInstance($className, $name);
    }

    public function addBean($obj, $name = null) {
        $this->add($name, $obj);
    }

    public function addBeanBuilderClass($className) {
        $this->addNewServiceInstance($className);
    }

    public static function setAutoLoadSupport($autoLoadSupport = true) {
        self::$autoLoadSupport = !!$autoLoadSupport;
    }

}
