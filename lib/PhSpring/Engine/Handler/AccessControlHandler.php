<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine\Handler;

use PhSpring\Annotation\Helper;
use PhSpring\Annotations\AccessControl;
use PhSpring\Annotations\ExceptionHandler;
use PhSpring\Annotations\Autowired;
use PhSpring\Annotations\Handler\IAnnotationHandler;
use PhSpring\Engine\Exceptions\UnAuthorizedException;
use PhSpring\Engine\IACLResource;
use PhSpring\Engine\IAuth;
use PhSpring\Engine\MethodInvoker;
use ReflectionClass;
use ReflectionMethod;
use Reflector;

/**
 * Description of AccessControl
 *
 * @author lobiferi
 */
class AccessControlHandler implements IAnnotationHandler {

    private static $defaultRole = null;

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
            if (Helper::getInstance()->hasAnnotation($reflMethod, AccessControl::class)) {
                $this->handleAnnotation($reflMethod, $instance);
            }
        } catch (UnAuthorizedException $ex) {
            $this->handleUnAuthorizedException($instance, $ex);
        }
    }

    private function handleAnnotation($reflMethod, $instance) {
        $annotation = Helper::getInstance()->getMethodAnnotation($reflMethod, AccessControl::class);
        $role = self::getRole();
        if (!$this->acl->isAllowed($role, $annotation->value)) {
            throw new UnAuthorizedException;
        }
    }

    private function handleUnAuthorizedException($instance, $ex) {
        $reflClass = new ReflectionClass($instance);
        $throwFurther = true;
        foreach ($reflClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (Helper::getInstance()->hasAnnotation($method, ExceptionHandler::class)) {
                $throwFurther &= MethodInvoker::invokeMethod($instance, $method->getName(), array());
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
