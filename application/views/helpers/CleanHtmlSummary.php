<?php

class Zend_View_Helper_CleanHtmlSummary extends Zend_View_Helper_Abstract
{
    private static $purifier = null;

    private function getPurifier()
    {
        $purifier = self::$purifier;
        if ($purifier == null) {
            $config = HTMLPurifier_Config::createDefault();
            $config->set('Cache.SerializerPath', APPLICATION_PATH.'/../data/cache/htmlpurifier');
            $config->set('AutoFormat.Linkify', 'true');
            $config->set('AutoFormat.AutoParagraph', 'true');
            $purifier = new HTMLPurifier($config);
        }
        return $purifier;
    }


    public function cleanHtmlSummary($html)
    {
        $purifier = $this->getPurifier();
        $cleanBody = $purifier->purify($html);
        return $cleanBody;
    }
}