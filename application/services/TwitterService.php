<?php

class Application_Service_TwitterService extends Zend_Rest_Client
{
    public function __construct()
    {
        $this->setUri("http://search.twitter.com");
        $this->setHeaders('Accept-Charset', 'utf-8');
    }

    public function search($params = array())
    {
        $cleanParams = array();
        foreach($params as $key => $value) {
            switch($key) {
                case 'q':
                case 'from':
                case 'geocode':
                case 'lang':
                case 'since_id':
                    $cleanParams[$key] = $value;
                    break;
                case 'rpp':
                    $cleanParams[$key] = (intval($value) > 100) ? 100 : intval($value);
                    break;
                case 'page':
                    $cleanParams[$key] = intval($value);
                    break;
                case 'show_user':
                    $cleanParams[$key] = 'true';
            }
        }
        $response = $this->restGet('/search.json', $cleanParams);
        
        return Zend_Json::decode($response->getBody());
    }
}
