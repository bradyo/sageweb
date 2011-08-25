<?php

class Application_View_Helper_Qurl extends Zend_View_Helper_Abstract
{
    public function qurl(array $urlOptions = array(), $name = null, $reset = false, $encode = true)
    {
        $url = $this->view->url($urlOptions, $name, $reset, $encode);
        $requestUri = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
        $query = parse_url($requestUri, PHP_URL_QUERY);
        if($query != '') {
            $url .= '/';
            $pairs = explode('&', $query);
            foreach ($pairs as $pair) {
                $url.= str_replace('=', '/', $pair) . '/';
            }
        }
        return $url;
    }
}