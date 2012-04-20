<?php

class Zend_View_Helper_CleanHtmlBody extends Zend_View_Helper_Abstract
{
    private static $purifier = null;

    private function getPurifier()
    {
        $purifier = self::$purifier;
        if ($purifier == null) {
            $config = HTMLPurifier_Config::createDefault();
            $config->set('Cache.SerializerPath', APPLICATION_PATH.'/../data/cache/htmlpurifier');
            $config->set('HTML.Doctype', 'XHTML 1.0 Strict');
            $config->set('HTML.Allowed', 'div,p,br,em,h1,h2,h3,h4,h5,strong,em,a[href|title],'
                .'ul,ol,li,code,pre,blockquote,img[src|alt|height|width|class]');
            $config->set('AutoFormat.Linkify', 'true');
            $config->set('AutoFormat.AutoParagraph', 'true');

            $purifier = new HTMLPurifier($config);
        }
        return $purifier;
    }


    public function cleanHtmlBody($html)
    {
        $purifier = $this->getPurifier();
        $cleanBody = $purifier->purify($html);
        return $cleanBody;
    }
}