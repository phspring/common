<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine;

/**
 * Default implementation of the {@link SessionAttributeStore} interface,
 * storing the attributes in the WebRequest session (i.e. HttpSession
 * or PortletSession).
 *
 * @author Juergen Hoeller
 * @since 2.5
 * @see #setAttributeNamePrefix
 * @see org.springframework.web.context.$request->WebRequest#setAttribute
 * @see org.springframework.web.context.$request->WebRequest#getAttribute
 * @see org.springframework.web.context.$request->WebRequest#removeAttribute
 */
class DefaultSessionAttributeStore implements SessionAttributeStore {

    private $attributeNamePrefix = "SessionAttributeStore";

    private function initSession() {
        if (session_status() !== PHP_SESSION_ACTIVE && session_status() !== PHP_SESSION_DISABLED) {
            session_start();
        }
    }

    /**
     * Specify a prefix to use for the attribute names in the backend session.
     * <p>Default is to use no prefix, storing the session attributes with the
     * same name as in the model.
     */
    public function setAttributeNamePrefix($attributeNamePrefix) {
        $this->attributeNamePrefix = ($attributeNamePrefix != null ? $attributeNamePrefix : "");
    }

    public function storeAttribute($attributeName, $attributeValue) {
//		Assert.notNull($request, "WebRequest must not be null");
//		Assert.notNull($attributeName, "Attribute name must not be null");
//		Assert.notNull($attributeValue, "Attribute value must not be null");
        $this->initSession();
        $_SESSION[$this->getAttributeNameInSession($attributeName)] = $attributeValue;
    }

    public function retrieveAttribute($attributeName) {
//		Assert.notNull($request, "WebRequest must not be null");
//		Assert.notNull($attributeName, "Attribute name must not be null");

        $this->initSession();
        return array_key_exists($this->getAttributeNameInSession($attributeName), $_SESSION) ? $_SESSION[$this->getAttributeNameInSession($attributeName)] : null;
    }

    public function cleanupAttribute($attributeName) {
//		Assert.notNull($request, "WebRequest must not be null");
//		Assert.notNull($attributeName, "Attribute name must not be null");
        $this->initSession();
        unset($_SESSION[$this->getAttributeNameInSession($attributeName)]);
    }

    /**
     * Calculate the attribute name in the backend session.
     * <p>The default implementation simply prepends the configured
     * {@link #setAttributeNamePrefix "$attributeNamePrefix"}, if any.
     * @param $request the current $request
     * @param $attributeName the name of the attribute
     * @return the attribute name in the backend session
     */
    protected function getAttributeNameInSession($attributeName) {
        return $this->attributeNamePrefix . $attributeName;
    }

}
