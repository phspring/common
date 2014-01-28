<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine\Handler;

use PHPUnit_Framework_TestCase;
use PhSpring\Service\Collection;
use PhSpring\TestFixtures\ClassInvokerFixture;
use PhSpring\TestFixtures\Singleton;
use ReflectionProperty;
use RuntimeException;

/**
 * Description of AutowiredHandlerTests
 *
 * @author lobiferi
 */
class AutowiredHandlerTests extends PHPUnit_Framework_TestCase{
    
    private $handler;
    
    protected function setUp() {
        $this->handler = new AutowiredHandler();
    }
    
    /**
     * @test
     * @expectedException RuntimeException
     * @expectedExceptionMessage Must set the property type by @var annotation or you must use @Quealifier annotation to define the service
     */
    public function withoutQualifierAndType() {
        $instance = new ClassInvokerFixture();
        $refl = new ReflectionProperty(ClassInvokerFixture::class, 'settedSingleton');
        $this->handler->run($refl , $instance);
    }
    
    /**
     * @test
     */
    public function withQualifier() {
        Collection::add('SingletonTestService', Singleton::getInstance());
        $instance = new ClassInvokerFixture();
        $refl = new ReflectionProperty(ClassInvokerFixture::class, 'singleton3');
        $this->handler->run($refl , $instance);
    }
    
    /**
     * @test
     */
    public function withType() {
        $instance = new ClassInvokerFixture();
        $refl = new ReflectionProperty(ClassInvokerFixture::class, 'singleton2');
        $this->handler->run($refl , $instance);
    }
    
    /**
     * @test
     */
    public function withQualifierAndType() {
        Collection::add('SingletonTestService', Singleton::getInstance());
        $instance = new ClassInvokerFixture();
        $refl = new ReflectionProperty(ClassInvokerFixture::class, 'singleton4');
        $this->handler->run($refl , $instance);
    }
    
    /**
     * @test
     * @expectedException RuntimeException
     * @expectedExceptionMessage Found service class mismatch with expected type! - name: 'SingletonTestService' type: 'PhSpring\Annotations\Autowired'
     */
    public function withQualifierAndWrongType() {
        Collection::add('SingletonTestService', Singleton::getInstance());
        $instance = new ClassInvokerFixture();
        $refl = new ReflectionProperty(ClassInvokerFixture::class, 'singleton5');
        $this->handler->run($refl , $instance);
    }
    
}
