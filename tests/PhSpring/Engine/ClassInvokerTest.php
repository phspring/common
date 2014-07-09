<?php

namespace PhSpring\Engine;

use PHPUnit_Framework_TestCase;
use PhSpring\Service\Collection;
use PhSpring\TestFixtures\ClassInvokerFixture;
use PhSpring\TestFixtures\Singleton;
use ReflectionClass;
use ReflectionProperty;
use RuntimeException;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-01-24 at 14:37:03.
 */
class ClassInvokerTest extends PHPUnit_Framework_TestCase {

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }
    /**
     * @covers PhSpring\Engine\ClassInvoker::getNewInstance
     * @test
     * @expectedException RuntimeException
     * @expectedExceptionMessage The constructor is not public in PhSpring\TestFixtures\Singleton class
     */
    public function theConstructorIsNotPublic() {
        ClassInvoker::getNewInstance(Singleton::class);
    }

    /**
     * @covers PhSpring\Engine\ClassInvoker::getNewInstance
     * @test
     * @expectedException RuntimeException
     */
    public function theConstructorIsNotPublicByReference() {
        ClassInvoker::getNewInstance(new ReflectionClass(Singleton::class));
    }
    /**
     * @test
     */
    public function validInvokeWithAutowiredProperty() {
        BeanFactory::getInstance()->addBeanClass(Singleton::class, 'SingletonTestService');
        $instance = ClassInvoker::getNewInstance(ClassInvokerFixture::class);
        $refl = new ReflectionProperty(ClassInvokerFixture::class, 'singleton');
        $refl->setAccessible(true);
        $this->assertTrue($refl->getValue($instance) instanceof Singleton);
    }
    
    /**
     * @test
     * @expectedException RuntimeException
     * @expectedExceptionMessage The className parameter must be string or instanceof ReflectionClass
     */
    public function invokeWithObject() {
        ClassInvoker::getNewInstance(new ClassInvokerFixture());
    }
    
    

}
