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
 * PHP response format.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */
class NP_Service_Gravatar_Profiles_ResponseFormat_Php
    extends NP_Service_Gravatar_Profiles_ResponseFormat_Abstract
    implements NP_Service_Gravatar_Profiles_ResponseFormat_ParserInterface
{
    protected $_id = 'php';

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
        $profile = @ unserialize($body);

        if ($profile === false) {
            require_once 'NP/Service/Gravatar/Profiles/ResponseFormat/Exception.php';
            throw new NP_Service_Gravatar_Profiles_ResponseFormat_Exception('Invalid PHP response.');
        }

        if (is_array($profile) && isset($profile['entry'])) { //Valid response?
            require_once 'NP/Service/Gravatar/Profiles/Profile.php';
            return new NP_Service_Gravatar_Profiles_Profile($profile['entry'][0]);
        }
        else { //Probably unexisting user is supplied.
            return $response;
        }
    }
}