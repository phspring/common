<?php

namespace PhSpring\Engine\Handler;

use PhSpring\Annotation\Helper;
use PhSpring\Annotations\RequestBody;
use PhSpring\Annotations\RequestParam;
use PhSpring\Annotations\Valid;
use PhSpring\Engine\AnnotationAbstract;
use PhSpring\Engine\BeanFactory;
use PhSpring\Engine\BindingResult;
use PhSpring\Engine\ClassInvoker;
use PhSpring\Engine\Constants;
use PhSpring\Engine\InvokerConfig;
use PhSpring\Reflection\ReflectionClass;
use PhSpring\Reflection\ReflectionMethod;
use PhSpring\Reflection\ReflectionProperty;
use ReflectionException;
use ReflectionParameter;
use Symfony\Component\Validator\ValidatorBuilder;
use Symfony\Component\Yaml\Exception\RuntimeException;

/**
 * Description of InvokeParameterHandler
 *
 * @author lobiferi
 */
class InvokeParameterHandler {

    /** @var array */
    private $annotations;

    /** @var ReflectionMethod  */
    private $reflMethod;

    /** @var array */
    private $args = null;

    /** @var array */
    private $invokeParams = array();

    /**
     *
     * @var array
     */
    private $handledParams = array();

    /**
     * 
     * @param array $annotations
     * @param ReflectionMethod $reflMethod
     * @param array $args
     */
    public function __construct(array $annotations, ReflectionMethod $reflMethod, array $args = null) {
        $this->annotations = $annotations;
        $this->reflMethod = $reflMethod;
        $this->args = $args;
    }

    private function hasAnnotation($annotationType, array $values = null) {
        foreach ($this->annotations as $annotation) {
            if ($annotation instanceof $annotationType && $this->checkAnnotationByValue($annotation, $values)) {
                return true;
            }
        }
        return false;
    }

    private function getAnnotation($annotationType, array $values = null) {
        foreach ($this->annotations as $annotation) {
            if ($annotation instanceof $annotationType && $this->checkAnnotationByValue($annotation, $values)) {
                return $annotation;
            }
        }
        return null;
    }

    /**
     * 
     * @param AnnotationAbstract $annotation
     * @param array $values
     * @return boolean
     */
    private function checkAnnotationByValue(AnnotationAbstract $annotation, array $values = null) {
        $found = true;
        if ($values !== null) {
            foreach ($values as $key => $value) {
                $found &= $annotation->$key == $value;
            }
        }
        return !!$found;
    }

    public function run() {
        if (!is_array($this->args)) {
            $this->args = (array) $this->args;
        }
        $this->invokeParams = array_values($this->args);
        $this->invokeValidator();

        foreach ($this->reflMethod->getParameters() as $parameter) {
            $this->setupParameterValue($parameter);
        }
        ksort($this->invokeParams);
        return $this->invokeParams;
    }

    private function setupParameterValue(ReflectionParameter $parameter) {
        $pos = $parameter->getPosition();
        if (in_array($pos, $this->handledParams, true)) {
            return;
        }

        $parameterName = $parameter->getName();
        $type = $this->getParameterType($parameter);

        if (array_key_exists($parameterName, $this->args)) {
            $this->invokeParams[$pos] = $this->args[$parameterName];
            return;
        }
        if (array_key_exists($pos, $this->args)) {
            $this->invokeParams[$pos] = $this->args[$pos];
            return;
        }

        $isPrimitiveType = (in_array($type, Constants::$php_default_types) || in_array($type, Constants::$php_pseudo_types));
        if ($parameter->isOptional()) {
            $this->invokeParams[$pos] = $parameter->getDefaultValue();
        }

        if ($isPrimitiveType || $type === null) {
            $this->handleRequestParam($parameter, $this->invokeParams);
        } elseif (!$isPrimitiveType && $type) {
            $this->invokeParams[$pos] = BeanFactory::getInstance()->getBean($type);
        }
    }

    /**
     * Return with the parameter type (string, int, ..., class name)
     * @param ReflectionParameter $parameter
     * @return null|string 
     */
    private function getParameterType(ReflectionParameter $parameter) {
        if ($parameter->getClass()) {
            return $parameter->getClass()->getName();
        }
        $matches = null;
        if (preg_match('/@param (\S*) \$' . $parameter->getName() . '/m', $this->reflMethod->getDocComment(), $matches)) {
            $types = preg_split('/|/', $matches[1]);
            $type = array_pop($types);
            return empty($type) ? null : $type;
        }
        return null;
    }

    /**
     * 
     * @param ReflectionParameter $parameter
     * @void
     */
    private function handleRequestParam(ReflectionParameter $parameter) {
        $parameterName = $parameter->getName();
        if ($this->hasAnnotation(RequestParam::class, array('value' => $parameterName))) {
            $annotation = $this->getAnnotation(RequestParam::class, array('value' => $parameterName));
            if ($annotation instanceof RequestBody) {
                $value = trim(trim(file_get_contents('php://input'), '"'));
            } else {
                $value = InvokerConfig::getRequestHelper()->getParam($parameterName);
            }
            $this->handleRequiredRequestParam($parameter, $annotation, $value);
            if ($annotation->defaultValue !== '****UNDEFINED****' && $value === null) {
                $value = $annotation->defaultValue;
            }
            $this->invokeParams[$parameter->getPosition()] = $value;
        }
    }

    private function handleRequiredRequestParam(ReflectionParameter $parameter, RequestParam $annotation, $value) {
        $parameterName = $parameter->getName();
        if ($annotation->required) {
            $isOptional = false;
            $isOptional |= $parameter->isOptional();
            try {
                $isOptional |= $parameter->getDefaultValueConstantName() !== null;
            } catch (ReflectionException $e) {
                
            }
            try {
                $isOptional |= $parameter->getDefaultValue() !== null;
            } catch (ReflectionException $e) {
                
            }
            if ($value === null && !$isOptional) {
                throw new RuntimeException("Parameter not found in the request: {$parameterName}");
            }
        }
    }

    private function invokeValidator() {
        if ($this->hasAnnotation(Valid::class)) {
            $valid = $this->getAnnotation(Valid::class);
            $params = $this->reflMethod->getParameters();
            $param = $valid->value;
            $parameter = current(array_filter($params, function($parameter)use ($param) {
                        return $parameter->getName() == $param;
                    }));
            $pos = $parameter->getPosition();
            $type = $this->getParameterType($parameter);
            $this->invokeParams[$pos] = $this->fillForm(ClassInvoker::getNewInstance($type));
            $this->handledParams[] = $pos;
            foreach ($params as $param) {
                if ($param->getClass() && $param->getClass()->getName() === BindingResult::class) {
                    $ppos = $param->getPosition();
                    $this->invokeParams[$ppos] = $this->bindingResultBuild($this->invokeParams[$pos]);
                    $this->handledParams[] = $ppos;
                }
            }
        }
    }

    private function bindingResultBuild($param) {
        $bind = new BindingResult();
        $builder = new ValidatorBuilder();
        $builder->enableAnnotationMapping();
        $result = $builder->getValidator()->validate($param);
        $bind->setResult($result);
        return $bind;
    }

    private function fillForm($form, $request = null) {
        $class = new ReflectionClass($form);
        if ($request === null) {
            $requestHelper = InvokerConfig::getRequestHelper();
            $request = $requestHelper->getParams();
        }
        /* @var $property ReflectionProperty */
        foreach ($class->getProperties() as $property) {
            $value = array_key_exists($property->getName(), $request) ? $request[$property->getName()] : null;
            $type = Helper::getPropertyType($property);
            if (is_array($value) && $type !== 'array') {
                $value = $this->fillForm(ClassInvoker::getNewInstance($type), $value);
            }
            $this->setFormFieldValue($property, $form, $value);
        }
        return $form;
    }

    private function setFormFieldValue(ReflectionProperty $property, $form, $value) {
        if ($value !== null) {
            if (!$property->isPublic()) {
                $property->setAccessible(true);
            }
            $property->setValue($form, $value);
        }
    }

}
