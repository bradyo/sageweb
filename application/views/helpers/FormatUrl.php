<?php

class Zend_View_Helper_FormatUrl extends Zend_View_Helper_Abstract
{
    /**
     * Makes a user submitted url absolute
     * @param string $url the url to format
     * @return string
     */
    public function formatUrl($url)
    {
        if (!preg_match('/^http:\/\//', $url)) {
            $url = 'http://' . $url;
        }
        return $url;
    }
}