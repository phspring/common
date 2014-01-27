<?php

namespace PhSpring\Engine\Handler;

use PHPUnit_Framework_TestCase;
use PhSpring\Annotation\Collection;
use PhSpring\Annotations\Autowired;
use PhSpring\Annotations\RequestParam;
use PhSpring\Engine\InvokerConfig;
use PhSpring\TestFixtures\InvokeParameterHandlerFixture;
use PhSpring\TestFixtures\RequestHelperFixture;
use ReflectionMethod;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-01-24 at 14:37:01.
 */
class InvokeParameterHandlerTest extends PHPUnit_Framework_TestCase {

    /**
     * @var InvokeParameterHandler
     */
    protected $object;

    /**
     *
     * @var IRequestHelper
     */
    protected $requestHelper;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function settingUp(array $annotations = null) {
        $this->requestHelper = new RequestHelperFixture();
        InvokerConfig::setRequestHelper($this->requestHelper);

        $reflMethod = new ReflectionMethod(InvokeParameterHandlerFixture::class, 'invokedMethod');
        if ($annotations === null) {
            $annotations = array(
                new Autowired(array()),
                new RequestParam(array('value' => 'id'))
            );
        }
        $collection = new Collection($annotations, $reflMethod);
        $this->object = new InvokeParameterHandler($collection, $reflMethod);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

    /**
     * @covers PhSpring\Engine\Handler\InvokeParameterHandler::run
     * @todo   Implement testRun().
     * @expectedException RuntimeException
     * @test
     */
    public function getRuntimeException() {
        $this->settingUp();
        $this->requestHelper->setParam('id', null);
        $this->object->run();
    }

    /**
     * @covers PhSpring\Engine\Handler\InvokeParameterHandler::run
     * @todo   Implement testRun().
     * @test
     */
    public function validWithId() {
        $this->settingUp();
        $this->requestHelper->setParam('id', 1);
        $this->object->run();
    }

    /**
     * @covers PhSpring\Engine\Handler\InvokeParameterHandler::run
     * @todo   Implement testRun().
     * @test
     */
    public function validWithUndefinedParameter() {
            $annotations = array(
                new Autowired(array()),
                new RequestParam(array('value' => 'id')),
                new RequestParam(array('value' => 'data'))
            );
        $this->settingUp($annotations);
        $this->requestHelper->setParam('id', 1);
        $this->object->run();
    }

}
