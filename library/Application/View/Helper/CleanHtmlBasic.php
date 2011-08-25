<?php

class Application_View_Helper_CleanHtmlBasic extends Zend_View_Helper_Abstract
{
    private static $purifier = null;

    private function getPurifier()
    {
        $purifier = self::$purifier;
        if ($purifier == null) {
            $config = HTMLPurifier_Config::createDefault();
            $config->set('Cache.SerializerPath', APPLICATION_PATH.'/../data/cache/htmlpurifier');
            $config->set('HTML.Doctype', 'XHTML 1.0 Strict');
            $config->set('HTML.Allowed', 'p,br,em,strong,em,a[href|title],ul,ol,li');
            $config->set('AutoFormat.Linkify', 'true');
            $config->set('AutoFormat.AutoParagraph', 'true');
            $purifier = new HTMLPurifier($config);
        }
        return $purifier;
    }


    public function cleanHtmlBasic($html)
    {
        $purifier = $this->getPurifier();
        $cleanBody = $purifier->purify($html);
        return $cleanBody;
    }
}