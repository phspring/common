<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Util;


/**
 * Assertion utility class that assists in validating arguments.
 * Useful for identifying programmer errors early and clearly at runtime.
 *
 * <p>For example, if the contract of a public method states it does not
 * allow {@code null} arguments, Assert can be used to validate that
 * contract. Doing this clearly indicates a contract violation when it
 * occurs and protects the class's invariants.
 *
 * <p>Typically used to validate method arguments rather than configuration
 * properties, to check for cases that are usually programmer errors rather than
 * configuration errors. In contrast to config initialization code, there is
 * usally no point in falling back to defaults in such methods.
 *
 * <p>This class is similar to JUnit's assertion library. If an argument value is
 * deemed invalid, an {@link IllegalArgumentException} is thrown (typically).
 * For example:
 *
 * <pre class="code">
 * Assert.notNull(clazz, "The class must not be null");
 * Assert.isTrue(i > 0, "The value must be greater than zero");</pre>
 *
 * Mainly for internal use within the framework; consider Jakarta's Commons Lang
 * >= 2.0 for a more comprehensive suite of assertion utilities.
 *
 */
abstract class Assert {

	/**
	 * Assert a boolean expression, throwing {@code IllegalArgumentException}
	 * if the test result is {@code false}.
	 * <pre class="code">Assert.isTrue(i &gt; 0, "The value must be greater than zero");</pre>
	 * @param expression a boolean expression
	 * @param message the exception message to use if the assertion fails
	 * @throws IllegalArgumentException if expression is {@code false}
	 */
	public static function isTrue(boolean $expression, $message="[Assertion failed] - this expression must be true") {
		if (!$expression) {
			throw new IllegalArgumentException($message);
		}
	}

	/**
	 * Assert that an object is {@code null} .
	 * <pre class="code">Assert.isNull(value, "The value must be null");</pre>
	 * @param object the object to check
	 * @param message the exception message to use if the assertion fails
	 * @throws IllegalArgumentException if the object is not {@code null}
	 */
	public static function isNull($object, $message = "[Assertion failed] - the object argument must be null") {
		if ($object !== null) {
			throw new IllegalArgumentException($message);
		}
	}

	/**
	 * Assert that an object is not {@code null} .
	 * <pre class="code">Assert.notNull(clazz, "The class must not be null");</pre>
	 * @param object the object to check
	 * @param message the exception message to use if the assertion fails
	 * @throws IllegalArgumentException if the object is {@code null}
	 */
	public static function notNull($object, $message="[Assertion failed] - this argument is required; it must not be null") {
		if ($object === null) {
			throw new IllegalArgumentException($message);
		}
	}

	/**
	 * Assert that the given String is not empty; that is,
	 * it must not be {@code null} and not the empty String.
	 * <pre class="code">Assert.hasLength(name, "Name must not be empty");</pre>
	 * @param text the String to check
	 * @param message the exception message to use if the assertion fails
	 * @see StringUtils#hasLength
	 */
	public static function hasLength($text, $message="[Assertion failed] - this String argument must have length; it must not be null or empty") {
		if (!mb_strlen($text)) {
			throw new IllegalArgumentException($message);
		}
	}

	/**
	 * Assert that the given String has valid text content; that is, it must not
	 * be {@code null} and must contain at least one non-whitespace character.
	 * <pre class="code">Assert.hasText(name, "'name' must not be empty");</pre>
	 * @param text the String to check
	 * @param message the exception message to use if the assertion fails
	 * @see StringUtils#hasText
	 */
	public static function hasText($text, $message="[Assertion failed] - this String argument must have text; it must not be null, empty, or blank") {
		if (!empty($text)) {
			throw new IllegalArgumentException($message);
		}
	}

	/**
	 * Assert that the given text does not contain the given substring.
	 * <pre class="code">Assert.doesNotContain(name, "rod", "Name must not contain 'rod'");</pre>
	 * @param textToSearch the text to search
	 * @param substring the substring to find within the text
	 * @param message the exception message to use if the assertion fails
	 */
	public static function doesNotContain($textToSearch, $substring, $message=null) {
            if($message===null){
                $message= "[Assertion failed] - this String argument must not contain the substring [" . $substring . "]";
            }
		if (mb_strlen($textToSearch) && mb_strlen($substring) &&
                mb_strpos($textToSearch, $substring)!==false) {
			throw new IllegalArgumentException($message);
		}
	}


	/**
	 * Assert that an array has elements; that is, it must not be
	 * {@code null} and must have at least one element.
	 * <pre class="code">Assert.notEmpty(array, "The array must have elements");</pre>
	 * @param array the array to check
	 * @param message the exception message to use if the assertion fails
	 * @throws IllegalArgumentException if the object array is {@code null} or has no elements
	 */
	public static function notEmpty(array $array, $message="[Assertion failed] - this array must not be empty: it must contain at least 1 element") {
		if (empty($array)) {
			throw new IllegalArgumentException($message);
		}
	}

	/**
	 * Assert that an array has no null elements.
	 * Note: Does not complain if the array is empty!
	 * <pre class="code">Assert.noNullElements(array, "The array must have non-null elements");</pre>
	 * @param array the array to check
	 * @param message the exception message to use if the assertion fails
	 * @throws IllegalArgumentException if the object array contains a {@code null} element
	 */
	public static function noNullElements(array $array, $message="[Assertion failed] - this array must not contain any null elements") {
		if ($array != null) {
			foreach ($array as $element) {
				if ($element == null) {
					throw new IllegalArgumentException($message);
				}
			}
		}
	}

	/**
	 * Assert that the provided object is an instance of the provided class.
	 * <pre class="code">Assert.instanceOf(Foo.class, foo);</pre>
	 * @param type the type to check against
	 * @param obj the object to check
	 * @param message a message which will be prepended to the message produced by
	 * the function itself, and which may be used to provide context. It should
	 * normally end in a ": " or ". " so that the function generate message looks
	 * ok when prepended to it.
	 * @throws IllegalArgumentException if the object is not an instance of clazz
	 * @see Class#isInstance
	 */
	public static function isInstanceOf($clazz, $obj, $message='') {
		self::notNull($clazz, "Type to check against must not be null");
		if (!($clazz instanceof $obj)) {
			throw new IllegalArgumentException(
					(mb_strlen($message) ? $message + " " : "") +
					"Object of class [" + ($obj != null ? get_class($obj) : "null") +
					"] must be an instance of " + $type);
		}
	}

	/**
	 * Assert that {@code superType.isAssignableFrom(subType)} is {@code true}.
	 * <pre class="code">Assert.isAssignable(Number.class, myClass);</pre>
	 * @param superType the super type to check against
	 * @param subType the sub type to check
	 * @param message a message which will be prepended to the message produced by
	 * the function itself, and which may be used to provide context. It should
	 * normally end in a ": " or ". " so that the function generate message looks
	 * ok when prepended to it.
	 * @throws IllegalArgumentException if the classes are not assignable
	 */
	public static function isAssignable($superType, $subType, $message='') {
		self::notNull($superType, "Type to check against must not be null");
		if ($subType == null || !$superType.isAssignableFrom($subType)) {
			throw new IllegalArgumentException($message + $subType + " is not assignable to " + $superType);
		}
	}


	/**
	 * Assert a boolean expression, throwing {@code IllegalStateException}
	 * if the test result is {@code false}. Call isTrue if you wish to
	 * throw IllegalArgumentException on an assertion failure.
	 * <pre class="code">Assert.state(id == null, "The id property must not already be initialized");</pre>
	 * @param expression a boolean expression
	 * @param message the exception message to use if the assertion fails
	 * @throws IllegalStateException if expression is {@code false}
	 */
	public static function state(boolean $expression, $message="[Assertion failed] - this state invariant must be true") {
		if (!$expression) {
			throw new IllegalStateException($message);
		}
	}
}