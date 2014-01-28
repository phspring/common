<?php

namespace PhSpring\Engine;

use PHPUnit_Framework_TestCase;
use PhSpring\TestFixtures\ClassInvokerFixture;
use ReflectionClass;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-01-24 at 14:37:02.
 */
class MethodInvokerTest extends PHPUnit_Framework_TestCase {

    /**
     * @var MethodInvoker
     */
    protected $object;

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

    /**
     * @covers PhSpring\Engine\MethodInvoker::invoke
     * @test
     */
    public function constructorInvokeWithoutArguments() {
        $ref = new ReflectionClass(ClassInvokerFixture::class);
        $instance = $ref->newInstanceWithoutConstructor();
        MethodInvoker::invoke($instance, '__construct');
        $property = $ref->getProperty('id');
        $property->setAccessible(true);
        $this->assertTrue($property->getValue($instance) === null);
    }

    /**
     * @covers PhSpring\Engine\MethodInvoker::invoke
     * @test
     */
    public function constructorInvokeWithArguments() {
        $ref = new ReflectionClass(ClassInvokerFixture::class);
        $instance = $ref->newInstanceWithoutConstructor();
        MethodInvoker::invoke($instance, '__construct', array('id'=>123));
        $property = $ref->getProperty('id');
        $property->setAccessible(true);
        $this->assertTrue($property->getValue($instance) === 123);
    }
    /**
     * @covers PhSpring\Engine\MethodInvoker::invoke
     * @test
     * @expectedException BadMethodCallException
     */
    public function callMethodWithoutExpectedArguments(){
        $ref = new ReflectionClass(ClassInvokerFixture::class);
        $instance = $ref->newInstanceWithoutConstructor();
        MethodInvoker::invoke($instance, 'setId');
    }
    /**
     * @test
     * @expectedException BadMethodCallException
     */
    public function callUndefinedMethod(){
        $ref = new ReflectionClass(ClassInvokerFixture::class);
        $instance = $ref->newInstanceWithoutConstructor();
        MethodInvoker::invoke($instance, 'undefinedMethod');
        
    }
}
