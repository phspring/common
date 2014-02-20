<?php

namespace PhSpring\Reflection;

use PHPUnit_Framework_TestCase;
use PhSpring\Annotation\Collection;
use PhSpring\Annotations\Qualifier;
use PhSpring\TestFixtures\ClassInvokerFixture;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-02-20 at 08:34:17.
 */
class ReflectionMethodTest extends PHPUnit_Framework_TestCase {

    /**
     * @var ReflectionMethod
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new ReflectionMethod(ClassInvokerFixture::class, 'setSingleton');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

    /**
     * @covers PhSpring\Reflection\ReflectionMethod::getAnnotation
     */
    public function testGetAnnotation() {
        $annotation = $this->object->getAnnotation(Qualifier::class);
        $this->assertInstanceOf(Qualifier::class, $annotation);
    }

    /**
     * @covers PhSpring\Reflection\ReflectionMethod::getAnnotations
     */
    public function testGetAnnotations() {
        $annotations = $this->object->getAnnotations();
        $this->assertInstanceOf(Collection::class, $annotations);
        $this->assertNotEmpty($annotations);
        $this->assertEquals(2, $annotations->count());
    }

    /**
     * @covers PhSpring\Reflection\ReflectionMethod::hasAnnotation
     */
    public function testHasAnnotation() {
        $annotation = $this->object->hasAnnotation(Qualifier::class);
        $this->assertTrue($annotation);
    }

    /**
     * @covers PhSpring\Reflection\ReflectionMethod::getDeclaringClass
     */
    public function testGetDeclaringClass() {
        $class = $this->object->getDeclaringClass();
        $this->assertInstanceOf(ReflectionClass::class, $class);
    }

}
