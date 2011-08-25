<?php
/**
 * NP-Gravatar
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is
 * bundled with this package in the file LICENSE.txt.
 */

require_once 'NP/Service/Gravatar/Profiles/ResponseFormat/Abstract.php';
require_once 'NP/Service/Gravatar/Profiles/ResponseFormat/ParserInterface.php';

/**
 * JSON response format.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */
class NP_Service_Gravatar_Profiles_ResponseFormat_Json
    extends NP_Service_Gravatar_Profiles_ResponseFormat_Abstract
    implements NP_Service_Gravatar_Profiles_ResponseFormat_ParserInterface
{
    protected $_id = 'json';

    /**
     * Parses response body and generates NP_Gravatar_Profile
     * instance.
     *
     * @param Zend_Http_Response $response
     * @return NP_Gravatar_Profile|Zend_Http_Response
     */
    public function profileFromHttpResponse(Zend_Http_Response $response)
    {
        $body = $response->getBody();

        try {
            require_once 'Zend/Json/Decoder.php';
            $profile = Zend_Json_Decoder::decode($body);
        }
        catch (Zend_Json_Exception $ex) {
            require_once 'NP/Service/Gravatar/Profiles/ResponseFormat/Exception.php';
            throw new NP_Service_Gravatar_Profiles_ResponseFormat_Exception('Invalid JSON response.');
        }

        if (isset($profile['entry'])) { //Valid response?
            require_once 'NP/Service/Gravatar/Profiles/Profile.php';
            return new NP_Service_Gravatar_Profiles_Profile($profile['entry'][0]);
        }
        else {
            return $response;
        }
    }
}