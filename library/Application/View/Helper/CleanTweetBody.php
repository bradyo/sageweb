<?php

class Application_View_Helper_CleanTweetBody extends Zend_View_Helper_Abstract
{
    private static $purifier = null;

    private function getPurifier()
    {
        $purifier = self::$purifier;
        if ($purifier == null) {
            $config = HTMLPurifier_Config::createDefault();
            $config->set('Cache.SerializerPath', APPLICATION_PATH.'/../data/cache/htmlpurifier');
            $config->set('HTML.Doctype', 'XHTML 1.0 Strict');
            $config->set('HTML.Allowed', 'em,strong,em,a[href|title]');
            $config->set('AutoFormat.Linkify', 'true');
            
            $purifier = new HTMLPurifier($config);
        }
        return $purifier;
    }


    public function cleanTweetBody($html)
    {
        $purifier = $this->getPurifier();
        $cleanBody = $purifier->purify($html);
        return $cleanBody;
    }
}