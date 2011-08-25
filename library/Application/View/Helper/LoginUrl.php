<?php

class Application_View_Helper_LoginUrl extends Zend_View_Helper_Abstract
{
    /**
     * Adds redirect uri to url route
     * @param string $url the url to format
     * @return string
     */
    public function loginUrl()
    {
        $destUri = $this->view->url();
        $url = $this->view->url(array(), 'login', true) . '?dest=' . $destUri;
        return $url;
    }
}