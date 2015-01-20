<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine;

/**
 * Strategy interface for storing model attributes in a backend session.
 *
 * @author lobiferi
 * @since 2.5
 * @see org.springframework.web.bind.annotation.SessionAttributes
 */
interface SessionAttributeStore {

	/**
	 * Store the supplied attribute in the backend session.
	 * <p>Can be called for new attributes as well as for existing attributes.
	 * In the latter case, this signals that the attribute value may have been modified.
	 * @param request the current request
	 * @param attributeName the name of the attribute
	 * @param attributeValue the attribute value to store
	 */
	function storeAttribute($attributeName, $attributeValue);

	/**
	 * Retrieve the specified attribute from the backend session.
	 * <p>This will typically be called with the expectation that the
	 * attribute is already present, with an exception to be thrown
	 * if this method returns {@code null}.
	 * @param request the current request
	 * @param attributeName the name of the attribute
	 * @return the current attribute value, or {@code null} if none
	 */
	function retrieveAttribute($attributeName);

	/**
	 * Clean up the specified attribute in the backend session.
	 * <p>Indicates that the attribute name will not be used anymore.
	 * @param request the current request
	 * @param attributeName the name of the attribute
	 */
	function cleanupAttribute($attributeName);

}
