<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine\Handler;

/**
 * Description of SessionAttributesHandler
 *
 * @author lobiferi
 */
class SessionAttributesHandler implements IAnnotationHandler {

    private $attributeNames = array();
    private $attributeTypes = array();
    private $knownAttributeNames = array();

    /**
     * @Autowired
     * @var SessionAttributeStore
     */
    private $sessionAttributeStore;

    public function run(Reflector $refl, $context) {
        
    }

    /**
     * Create a new instance for a controller type. Session attribute names and
     * types are extracted from the {@code @SessionAttributes} annotation, if
     * present, on the given type.
     * @param handlerType the controller type
     * @param sessionAttributeStore used for session access
     */
    public function __construct($annotation) {
        $this->annotation = $annotation;
        if ($this->annotation != null) {
            $this->attributeNames += (array) $this->annotation->value;
            $this->attributeTypes += (array) $this->annotation->types;
        }

        foreach ($this->attributeNames as $attributeName) {
            if (!in_array($attributeName, $this->knownAttributeNames)) {
                $this->knownAttributeNames[] = $attributeName;
            }
        }
    }

    /**
     * Whether the controller represented by this instance has declared any
     * session attributes through an {@link SessionAttributes} annotation.
     */
    public function hasSessionAttributes() {
        return ((count($this->attributeNames) > 0) || (count($this->attributeTypes) > 0));
    }

    /**
     * Whether the attribute name or type match the names and types specified
     * via {@code @SessionAttributes} in underlying controller.
     *
     * <p>Attributes successfully resolved through this method are "remembered"
     * and subsequently used in {@link #retrieveAttributes(WebRequest)} and
     * {@link #cleanupAttributes(WebRequest)}.
     *
     * @param attributeName the attribute name to check, never {@code null}
     * @param attributeType the type for the attribute, possibly {@code null}
     */
    public function isHandlerSessionAttribute($attributeName, $attributeType) {
        if (in_array($attributeName, $this->attributeNames) || in_array($attributeType, $this->attributeTypes)) {
            $this->knownAttributeNames[] = $attributeName;
            return true;
        } else {
            return false;
        }
    }

    /**
     * Store a subset of the given attributes in the session. Attributes not
     * declared as session attributes via {@code @SessionAttributes} are ignored.
     * @param attributes candidate attributes for session storage
     */
    public function storeAttributes(array $attributes) {
        foreach ($attributes as $name => $value) {
            $attrType = ($value != null) ? get_class($value) : null;

            if ($this->isHandlerSessionAttribute($name, $attrType)) {
                $this->sessionAttributeStore->storeAttribute($name, $value);
            }
        }
    }

    /**
     * Retrieve "known" attributes from the session, i.e. attributes listed
     * by name in {@code @SessionAttributes} or attributes previously stored
     * in the model that matched by type.
     * @return a map with handler session attributes, possibly empty
     */
    public function retrieveAttributes() {
        $attributes = array();
        foreach ($this->knownAttributeNames as $name) {
            $value = $this->sessionAttributeStore->retrieveAttribute($name);
            if ($value != null) {
                $attributes[$name] = $value;
            }
        }
        return $attributes;
    }

    /**
     * Remove "known" attributes from the session, i.e. attributes listed
     * by name in {@code @SessionAttributes} or attributes previously stored
     * in the model that matched by type.
     */
    public function cleanupAttributes() {
        foreach ($this->knownAttributeNames as $attributeName) {
            $this->sessionAttributeStore->cleanupAttribute($attributeName);
        }
    }

    /**
     * A pass-through call to the underlying {@link SessionAttributeStore}.
     * @param attributeName the name of the attribute of interest
     * @return the attribute value or {@code null}
     */
    protected function retrieveAttribute($attributeName) {
        return $this->sessionAttributeStore->retrieveAttribute($attributeName);
    }
    
    public function __destruct() {
        ;
    }

}
