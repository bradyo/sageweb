<?php
/**
 * NP-Gravatar
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is
 * bundled with this package in the file LICENSE.txt.
 */

/**
 * Client for consuming profile information, based on the primary
 * email address of some user.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */
class NP_Service_Gravatar_Profiles
{
    const GRAVATAR_SERVER = 'http://www.gravatar.com';

    /**
     * HTTP Client used to query web services.
     * 
     * @var Zend_Http_Client 
     */
    protected $_httpClient = null;

    /**
     * Response format.
     * 
     * @var NP_Service_Gravatar_Profiles_ResponseFormat_Abstract
     */
    protected $_responseFormat;

    /**
     * Default response format.
     *
     * @var NP_Service_Gravatar_Profiles_ResponseFormat_Abstract
     */
    protected static $_defaultResponseFormat;

    /**
     * Constructor.
     *
     * @return void
     */
    public function  __construct()
    {
        $this->_setupResponseFormat();
    }

    /**
     * Gets HTTP client instance.
     *
     * @return Zend_Http_Client
     */
    public function getHttpClient()
    {
        if (!$this->_httpClient instanceof Zend_Http_Client) {
            require_once 'Zend/Http/Client.php';
            $this->_httpClient = new Zend_Http_Client();
        }

        return $this->_httpClient;
    }

    /**
     * Sets up response format instance.
     *
     * @return void
     */
    protected function _setupResponseFormat()
    {
        if (!$this->_responseFormat) {
            if (!self::$_defaultResponseFormat instanceof NP_Service_Gravatar_Profiles_ResponseFormat_Abstract) {
                require_once 'NP/Service/Gravatar/Profiles/ResponseFormat/Php.php';
                $this->_responseFormat = new NP_Service_Gravatar_Profiles_ResponseFormat_Php();
            }
            else {
                $this->_responseFormat = self::$_defaultResponseFormat;
            }
        }
    }

    /**
     * Sets the response format.
     *
     * @param NP_Service_Gravatar_Profiles_ResponseFormat_Abstract $responseFormat
     * @return NP_Service_Gravatar
     */
    public function setResponseFormat(NP_Service_Gravatar_Profiles_ResponseFormat_Abstract $responseFormat)
    {
        $this->_responseFormat = $responseFormat;
        
        return $this;
    }

    /**
     * Gets the response format.
     * 
     * @return NP_Service_Gravatar_Profiles_ResponseFormat_Abstract
     */
    public function getResponseFormat()
    {
        return $this->_responseFormat;
    }

    /**
     * Sets default response format.
     *
     * @param NP_Service_Gravatar_Profiles_ResponseFormat_Abstract $responseFormat
     * @return void
     */
    public static function setDefaultResponseFormat(NP_Service_Gravatar_Profiles_ResponseFormat_Abstract $responseFormat)
    {
        self::$_defaultResponseFormat = $responseFormat;
    }

    /**
     * Gets default response format.
     *
     * @return NP_Service_Gravatar_Profiles_ResponseFormat_Abstract
     */
    public static function getDefaultResponseFormat()
    {
        return self::$_defaultResponseFormat;
    }

    /**
     * Gets profile info of some Gravatar's user, based on his/her
     * email address. Return value is NP_Gravatar_Profile instance,
     * in case $_responseFormat implements
     * NP_Service_Gravatar_Profiles_ResponseFormat_ParserInterface
     * interface. Otherwise, or in case $rawResponse flag is set to
     * boolean true, Zend_Http_Response instance is returned.
     *
     * @param string $email
     * @param bool $rawResponse Whether raw response object should be returned.
     * @return NP_Gravatar_Profile|Zend_Http_Response
     */
    public function getProfileInfo($email, $rawResponse = false)
    {
        require_once 'NP/Service/Gravatar/Utility.php';
        $email = strtolower(trim((string)$email));
        $hash = NP_Service_Gravatar_Utility::emailHash($email);

        $responseFormat = $this->getResponseFormat();

        $response = $this->getHttpClient()->setMethod(Zend_Http_Client::GET)
              ->setUri(self::GRAVATAR_SERVER . '/' . $hash . '.' . $responseFormat->__toString())
              ->request();

        $reflected = new ReflectionObject($responseFormat);
        if (
        $reflected->implementsInterface('NP_Service_Gravatar_Profiles_ResponseFormat_ParserInterface')
        && !$rawResponse
         ) {
            return $responseFormat->profileFromHttpResponse($response);
        }
        else {
            return $response;
        }
    }
}