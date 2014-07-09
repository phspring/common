<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine;

/*
 * Copyright 2002-2013 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * Creates {@link RequestMappingInfo} instances from type and method-level
 * {@link RequestMapping @RequestMapping} annotations in
 * {@link Controller @Controller} classes.
 *
 * @author Arjen Poutsma
 * @author Rossen Stoyanchev
 * @since 3.1
 */
class RequestMappingHandlerMapping/* extends RequestMappingInfoHandlerMapping
  implements EmbeddedValueResolverAware */ {

    /**
     * @var boolean
     */
    private $useSuffixPatternMatch = true;

    /**
     * @var boolean
     */
    private $useRegisteredSuffixPatternMatch = false;

    /**
     * @var boolean
     */
    private $useTrailingSlashMatch = true;

    /**
     * @var ContentNegotiationManager
     */
    private $contentNegotiationManager; // = new ContentNegotiationManager();

    /**
     * @var array
     * @final
     */
    private $fileExtensions = array();

    /**
     * @var StringValueResolver
     */
    private $embeddedValueResolver;

    /**
     * Whether to use suffix pattern match (".*") when matching patterns to
     * requests. If enabled a method mapped to "/users" also matches to "/users.*".
     * <p>The default value is {@code true}.
     * <p>Also see {@link #setUseRegisteredSuffixPatternMatch(boolean)} for
     * more fine-grained control over specific suffixes to allow.
     */
    public function setUseSuffixPatternMatch(boolean $useSuffixPatternMatch) {
        $this->useSuffixPatternMatch = $useSuffixPatternMatch;
    }

    /**
     * Whether to use suffix pattern match for registered file extensions only
     * when matching patterns to requests.
     *
     * <p>If enabled, a controller method mapped to "/users" also matches to
     * "/users.json" assuming ".json" is a file extension registered with the
     * provided {@link #setContentNegotiationManager(ContentNegotiationManager)
     * contentNegotiationManager}. This can be useful for allowing only specific
     * URL extensions to be used as well as in cases where a "." in the URL path
     * can lead to ambiguous interpretation of path variable content, (e.g. given
     * "/users/{user}" and incoming URLs such as "/users/john.j.joe" and
     * "/users/john.j.joe.json").
     *
     * <p>If enabled, this flag also enables
     * {@link #setUseSuffixPatternMatch(boolean) useSuffixPatternMatch}. The
     * default value is {@code false}.
     */
    public function setUseRegisteredSuffixPatternMatch(boolean $useRegsiteredSuffixPatternMatch) {
        $this->useRegisteredSuffixPatternMatch = $useRegsiteredSuffixPatternMatch;
        $this->useSuffixPatternMatch = $useRegsiteredSuffixPatternMatch ? true : $this->useSuffixPatternMatch;
    }

    /**
     * Whether to match to URLs irrespective of the presence of a trailing slash.
     * If enabled a method mapped to "/users" also matches to "/users/".
     * <p>The default value is {@code true}.
     */
    public function setUseTrailingSlashMatch(boolean $useTrailingSlashMatch) {
        $this->useTrailingSlashMatch = $useTrailingSlashMatch;
    }

    /**
     * @Override
     */
    public function setEmbeddedValueResolver(StringValueResolver $resolver) {
        $this->embeddedValueResolver = $resolver;
    }

    /**
     * Set the {@link ContentNegotiationManager} to use to determine requested media types.
     * If not set, the default constructor is used.
     */
    public function setContentNegotiationManager(ContentNegotiationManager $contentNegotiationManager) {
        Assert::notNull($contentNegotiationManager);
        $this->contentNegotiationManager = $contentNegotiationManager;
    }

    /**
     * Whether to use suffix pattern matching.
     * @return boolean
     */
    public function useSuffixPatternMatch() {
        return $this->useSuffixPatternMatch;
    }

    /**
     * Whether to use registered suffixes for pattern matching.
     * @return boolean
     */
    public function useRegisteredSuffixPatternMatch() {
        return $this->useRegisteredSuffixPatternMatch;
    }

    /**
     * Whether to match to URLs irrespective of the presence of a trailing  slash.
     * @return boolean
     */
    public function useTrailingSlashMatch() {
        return $this->useTrailingSlashMatch;
    }

    /**
     * Return the configured {@link ContentNegotiationManager}.
     * @return ContentNegotiationManager
     */
    public function getContentNegotiationManager() {
        return $this->contentNegotiationManager;
    }

    /**
     * Return the file extensions to use for suffix pattern matching.
     * @return array[string]
     */
    public function getFileExtensions() {
        return $this->fileExtensions;
    }

    /**
     * @Override
     */
    public function afterPropertiesSet() {
        if ($this->useRegisteredSuffixPatternMatch) {
            $this->fileExtensions->addAll($this->contentNegotiationManager->getAllFileExtensions());
        }
        parent::afterPropertiesSet();
    }

    /**
     * Expects a handler to have a type-level @{@link Controller} annotation.
     * @Override
     */
    protected function isHandler($beanType) {
        return ((AnnotationUtils::findAnnotation($beanType, Controller::class) != null) ||
                (AnnotationUtils::findAnnotation($beanType, RequestMapping::class) != null));
    }

    /**
     * Uses method and type-level @{@link RequestMapping} annotations to create
     * the RequestMappingInfo.
     *
     * @return RequestMappingInfo the created RequestMappingInfo, or {@code null} if the method
     * does not have a {@code @RequestMapping} annotation.
     *
     * @see #getCustomMethodCondition(Method)
     * @see #getCustomTypeCondition(Class)
     * @Override
     */
    protected function getMappingForMethod($method, $handlerType) {
        /** @var $info RequestMappingInfo */
        $info = null;
        /** @var $methodAnnotation RequestMapping */
        $methodAnnotation = AnnotationUtils::findAnnotation($method, RequestMapping::class);
        if ($methodAnnotation != null) {
            /** @var $methodCondition RequestCondition */
            $methodCondition = getCustomMethodCondition($method);
            $info = createRequestMappingInfo($methodAnnotation, $methodCondition);
            /** @var $typeAnnotation RequestMapping */
            $typeAnnotation = AnnotationUtils::findAnnotation($handlerType, RequestMapping::class);
            if ($typeAnnotation != null) {
                /** @var $typeCondition RequestCondition */
                $typeCondition = getCustomTypeCondition($handlerType);
                $info = createRequestMappingInfo($typeAnnotation, $typeCondition)->combine($info);
            }
        }
        return $info;
    }

    /**
     * Provide a custom type-level request condition.
     * The custom {@link RequestCondition} can be of any type so long as the
     * same condition type is returned from all calls to this method in order
     * to ensure custom request conditions can be combined and compared.
     *
     * <p>Consider extending {@link AbstractRequestCondition} for custom
     * condition types and using {@link CompositeRequestCondition} to provide
     * multiple custom conditions.
     *
     * @param handlerType the handler type for which to create the condition
     * @return RequestCondition the condition, or {@code null}
     */
    protected function getCustomTypeCondition($handlerType) {
        return null;
    }

    /**
     * Provide a custom method-level request condition.
     * The custom {@link RequestCondition} can be of any type so long as the
     * same condition type is returned from all calls to this method in order
     * to ensure custom request conditions can be combined and compared.
     *
     * <p>Consider extending {@link AbstractRequestCondition} for custom
     * condition types and using {@link CompositeRequestCondition} to provide
     * multiple custom conditions.
     *
     * @param method the handler method for which to create the condition
     * @return RequestCondition the condition, or {@code null}
     */
    protected function getCustomMethodCondition($method) {
        return null;
    }

    /**
     * Created a RequestMappingInfo from a RequestMapping annotation.
     * @return RequestMappingInfo
     */
    protected function createRequestMappingInfo(RequestMapping $annotation, RequestCondition $customCondition) {

        /** 	@var $patterns array[string] */
        $patterns = resolveEmbeddedValuesInPatterns($annotation->value);
        return new RequestMappingInfo(
                new PatternsRequestCondition($patterns, $this->getUrlPathHelper(), $this->getPathMatcher(), $this->useSuffixPatternMatch, $this->useTrailingSlashMatch, $this->fileExtensions), new RequestMethodsRequestCondition($annotation->method), new ParamsRequestCondition($annotation->params()), new HeadersRequestCondition($annotation->headers()), new ConsumesRequestCondition($annotation->consumes(), $annotation->headers()), new ProducesRequestCondition($annotation->produces(), $annotation->headers(), $this->getContentNegotiationManager()), $customCondition);
    }

    /**
     * Resolve placeholder values in the given array of patterns.
     * @return array[string] a new array with updated patterns
     */
    protected function resolveEmbeddedValuesInPatterns(array $patterns) {
        if ($this . embeddedValueResolver == null) {
            return $patterns;
        } else {
            $resolvedPatterns = array();
            for ($i = 0; $i < sizeof($patterns); $i++) {
                $resolvedPatterns[$i] = $this->embeddedValueResolver->resolveStringValue($patterns[$i]);
            }
            return $resolvedPatterns;
        }
    }

}
