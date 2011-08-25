<?php

class Application_View_Helper_QueryString extends Zend_View_Helper_Abstract {
    
    public function queryString($replaceParams = null) {
        if ($replaceParams === null) {
            $replaceParams = array();
        }
        
        $queryParams = $replaceParams;
        $requestUri = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
        $queryString = parse_url(urldecode($requestUri), PHP_URL_QUERY);
        if ($queryString != '') {
            // extract paramters as array
            parse_str($queryString, $queryParams);
        }
        
        // remove null params from result
        foreach ($replaceParams as $key => $value) {
            if ($value === null) {
                unset($queryParams[$key]);
            } else {
                $queryParams[$key] = $value;
            }
        }
        
        $newQueryString = '';
        if (count($queryParams) > 0) {
            $newQueryString = '?' . http_build_query($queryParams);
        }
        return $newQueryString;
    }
}