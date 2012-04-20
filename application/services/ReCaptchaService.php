<?php

class Application_Service_ReCaptchaService extends Zend_Service_ReCaptcha
{
    public function __construct($params = null, $options = null, $ip = null)
    {
        $recaptchaKeys = Zend_Registry::get('config.recaptcha');
        $publicKey = $recaptchaKeys['publicKey'];
        $privateKey = $recaptchaKeys['privateKey'];
        
        parent::__construct($publicKey, $privateKey, $params, $options, $ip);
    }
}