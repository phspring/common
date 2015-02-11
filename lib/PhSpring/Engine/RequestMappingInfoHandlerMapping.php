<?php
die('This is not working!! This is temporally!! '. __FILE__);
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine;

/**
 * Description of RequestMappingInfoHandlerMapping
 *
 * @author lobiferi
 */
abstract class RequestMappingInfoHandlerMapping /* extends AbstractHandlerMethodMapping */{

	/**
	 * Get the URL path patterns associated with this {@link RequestMappingInfo}.
         * 	@Override
         * @return array
	 */
	protected function getMappingPathPatterns(RequestMappingInfo $info) {
		return $info.getPatternsCondition().getPatterns();
	}

	/**
	 * Check if the given RequestMappingInfo matches the current request and
	 * return a (potentially new) instance with conditions that match the
	 * current request -- for example with a subset of URL patterns.
	 * @return RequestMappingInfo an info in case of a match; or {@code null} otherwise.
         * @Override
	 */
	
	protected function getMatchingMapping(RequestMappingInfo $info, HttpServletRequest $request) {
		return $info.getMatchingCondition($request);
	}

	/**
	 * Provide a Comparator to sort RequestMappingInfos matched to a request.
         * @Override
         * @return Comparator
	 */
	
	protected function getMappingComparator(HttpServletRequest $request) {
		return new Comparator(function(RequestMappingInfo $info1, RequestMappingInfo $info2) {
				return $info1.compareTo($info2, $request);
			});
	}

	/**
	 * Expose URI template variables, matrix variables, and producible media types in the request.
	 * @see HandlerMapping#URI_TEMPLATE_VARIABLES_ATTRIBUTE
	 * @see HandlerMapping#MATRIX_VARIABLES_ATTRIBUTE
	 * @see HandlerMapping#PRODUCIBLE_MEDIA_TYPES_ATTRIBUTE
         * @Override
         * @param RequestMappingInfo $info
         * @param string $lookupPath
         * @param HttpServletRequest $request
	 */
	protected function handleMatch(RequestMappingInfo $info, $lookupPath, HttpServletRequest $request) {
		parent::handleMatch($info, $lookupPath, $request);

		$bestPattern;
		$uriVariables = array();
		$decodedUriVariables = array();

		$patterns = $info->getPatternsCondition()->getPatterns();
		if (empty($patterns)) {
			$bestPattern = $lookupPath;
		}
		else {
			$bestPattern = next($patterns);
			$uriVariables = $this->getPathMatcher()->extractUriTemplateVariables($bestPattern, $lookupPath);
			$decodedUriVariables = getUrlPathHelper()->decodePathVariables($request, $uriVariables);
		}

		$request->setAttribute(BEST_MATCHING_PATTERN_ATTRIBUTE, $bestPattern);
		$request->setAttribute(HandlerMapping::URI_TEMPLATE_VARIABLES_ATTRIBUTE, $decodedUriVariables);

		if ($this->isMatrixVariableContentAvailable()) {
			$matrixVars = $this->extractMatrixVariables($request, $uriVariables);
			$request.setAttribute(HandlerMapping::MATRIX_VARIABLES_ATTRIBUTE, $matrixVars);
		}

		if (!empty($info->getProducesCondition()->getProducibleMediaTypes())) {
			$mediaTypes = $info->getProducesCondition()->getProducibleMediaTypes();
			$request->setAttribute(PRODUCIBLE_MEDIA_TYPES_ATTRIBUTE, $mediaTypes);
		}
	}

	/**
         * @return boolean
         */
        private function isMatrixVariableContentAvailable() {
		return !$this->getUrlPathHelper()->shouldRemoveSemicolonContent();
	}

	/**
         * @return array
         */
        private function extractMatrixVariables(
			HttpServletRequest $request, array $uriVariables) {

		$result = array();
		foreach ($uriVariables as $uriVarKey => $uriVarValue) {

			$equalsIndex = strpos($uriVarValue, '=');
			if ($equalsIndex == -1) {
				continue;
			}

			$matrixVariables = '';

			$semicolonIndex = $equalsIndex = strpos($uriVarValue, ':');
			if (($semicolonIndex === false) || ($semicolonIndex == 0) || ($equalsIndex < $semicolonIndex)) {
				$matrixVariables = $uriVarValue;
			}
			else {
				$matrixVariables = substr($uriVarValue, $semicolonIndex + 1);
				$uriVariables[$uriVarKey] = substr($uriVarValue, 0, $semicolonIndex);
			}

			$vars = WebUtils.parseMatrixVariables(matrixVariables);
			$result[$uriVarKey] = $this->getUrlPathHelper()->decodeMatrixVariables($request, $vars);
		}
		return $result;
	}

	/**
	 * Iterate all RequestMappingInfos once again, look if any match by URL at
	 * least and raise exceptions accordingly.
	 * @throws HttpRequestMethodNotSupportedException if there are matches by URL
	 * but not by HTTP method
	 * @throws HttpMediaTypeNotAcceptableException if there are matches by URL
	 * but not by consumable/producible media types
         * @Override
         * @return HandlerMethod
	 */
	
	protected function handleNoMatch($requestMappingInfos,
			$lookupPath, HttpServletRequest $request) {

		$allowedMethods = array();

		$patternMatches = array();
		$patternAndMethodMatches = array();

		foreach ($requestMappingInfos as $info ) {
			if ($info.getPatternsCondition().getMatchingCondition($request) != null) {
				$patternMatches.add(info);
				if ($info.getMethodsCondition().getMatchingCondition($request) != null) {
					$patternAndMethodMatches.add(info);
				}
				else {
					foreach ($info.getMethodsCondition().getMethods() as $method) {
						$allowedMethods.add($method.name());
					}
				}
			}
		}

		if ($patternMatches.isEmpty()) {
			return null;
		}
		else if ($patternAndMethodMatches.isEmpty() && !$allowedMethods.isEmpty()) {
			throw new HttpRequestMethodNotSupportedException($request.getMethod(), $allowedMethods);
		}

		$consumableMediaTypes=array();
		$producibleMediaTypes=array();
		$paramConditions=array();

		if ($patternAndMethodMatches.isEmpty()) {
			$consumableMediaTypes = getConsumableMediaTypes($request, $patternMatches);
			$producibleMediaTypes = getProducibleMediaTypes($request, $patternMatches);
			$paramConditions = getRequestParams($request, $patternMatches);
		}
		else {
			$consumableMediaTypes = getConsumableMediaTypes($request, $patternAndMethodMatches);
			$producibleMediaTypes = getProducibleMediaTypes($request, $patternAndMethodMatches);
			$paramConditions = getRequestParams($request, $patternAndMethodMatches);
		}

		if (!consumableMediaTypes.isEmpty()) {
			$contentType = null;
			if (StringUtils.hasLength($request.getContentType())) {
				try {
					$contentType = MediaType.parseMediaType($request.getContentType());
				}
				catch (IllegalArgumentException $ex) {
					throw new HttpMediaTypeNotSupportedException(ex.getMessage());
				}
			}
			throw new HttpMediaTypeNotSupportedException($contentType, (array)$consumableMediaTypes);
		}
		else if (!$producibleMediaTypes.isEmpty()) {
			throw new HttpMediaTypeNotAcceptableException($producibleMediaTypes);
		}
		else if (!CollectionUtils.isEmpty($paramConditions)) {
			$params = $paramConditions.toArray(paramConditions.size());
			throw new UnsatisfiedServletRequestParameterException(params, $request.getParameterMap());
		}
		else {
			return null;
		}
	}

	private Set<MediaType> getConsumableMediaTypes(HttpServletRequest $request, Set<RequestMappingInfo> partialMatches) {
		Set<MediaType> result = new HashSet<MediaType>();
		for (RequestMappingInfo partialMatch : partialMatches) {
			if (partialMatch.getConsumesCondition().getMatchingCondition($request) == null) {
				result.addAll(partialMatch.getConsumesCondition().getConsumableMediaTypes());
			}
		}
		return result;
	}

	private Set<MediaType> getProducibleMediaTypes(HttpServletRequest $request, Set<RequestMappingInfo> partialMatches) {
		Set<MediaType> result = new HashSet<MediaType>();
		for (RequestMappingInfo partialMatch : partialMatches) {
			if (partialMatch.getProducesCondition().getMatchingCondition($request) == null) {
				result.addAll(partialMatch.getProducesCondition().getProducibleMediaTypes());
			}
		}
		return result;
	}

	private Set<String> getRequestParams(HttpServletRequest $request, Set<RequestMappingInfo> partialMatches) {
		for (RequestMappingInfo partialMatch : partialMatches) {
			ParamsRequestCondition condition = partialMatch.getParamsCondition();
			if (!CollectionUtils.isEmpty(condition.getExpressions()) && (condition.getMatchingCondition($request) == null)) {
				Set<String> expressions = new HashSet<String>();
				for (NameValueExpression expr : condition.getExpressions()) {
					expressions.add(expr.toString());
				}
				return expressions;
			}
		}
		return null;
	}

}