<?php

class Zend_View_Helper_RegisterUrl extends Zend_View_Helper_Abstract
{
    /**
     * Adds redirect uri to url route
     * @param string $url the url to format
     * @return string
     */
    public function registerUrl()
    {
        $destUri = $this->view->url();
        $url = $this->view->url(array(), 'register', true) . '?dest=' . $destUri;
        return $url;
    }
}