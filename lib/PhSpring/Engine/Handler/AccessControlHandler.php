<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine\Handler;

use PhSpring\Annotation\Helper;
use PhSpring\Annotations\AccessControl;
use PhSpring\Annotations\Autowired;
use PhSpring\Annotations\ExceptionHandler;
use PhSpring\Engine\Exceptions\UnAuthorizedException;
use PhSpring\Engine\IACLResource;
use PhSpring\Engine\IAuth;
use PhSpring\Engine\MethodInvoker;
use PhSpring\Reflection\ReflectionClass;
use PhSpring\Reflection\ReflectionMethod;
use Reflector;

/**
 * Description of AccessControl
 *
 * @author lobiferi
 */
class AccessControlHandler implements IAnnotationHandler {

    private static $defaultRole = 'guest';

    /**
     * @var IAuth
     */
    private $auth;

    /**
     * @var IACLResource
     */
    private $acl;
    
    
    /**
     * @Autowired
     * @param IAuth $auth
     * @param IACLResource $acl
     */
    public function __construct(IAuth $auth, IACLResource $acl) {
        $this->acl = $acl;
        $this->auth = $auth;
    }
    
    public static function setDefaultRole($defaultRole) {
        self::$defaultRole = $defaultRole;
    }

    public function run(Reflector $reflMethod, $instance) {
        try {
            if (Helper::hasAnnotation($reflMethod, AccessControl::class)) {
                $this->handleAnnotation($reflMethod, $instance);
            }
        } catch (UnAuthorizedException $ex) {
            $this->handleUnAuthorizedException($instance, $ex);
        }
    }

    private function handleAnnotation(ReflectionMethod $reflMethod, $instance) {
        $annotation = $reflMethod->getAnnotation(AccessControl::class);
        $role = self::getRole();
        if (!$this->acl->isAllowed($role, $annotation->value)) {
            throw new UnAuthorizedException;
        }
    }

    private function handleUnAuthorizedException($instance, $ex) {
        $reflClass = new ReflectionClass($instance);
        $throwFurther = true;
        foreach ($reflClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->hasAnnotation(ExceptionHandler::class)) {
                $throwFurther &= MethodInvoker::invoke($instance, $method->getName(), array());
            }
        }
        if ($throwFurther) {
            throw $ex;
        }
    }

    private function getRole() {
        $role = $this->getDefaultRole();
        if ($this->auth->isAuthenticated()) {
            $role = $this->auth->getUserRole();
        }
        return $role;
    }


    private function getDefaultRole() {
        return self::$defaultRole;
    }

}
