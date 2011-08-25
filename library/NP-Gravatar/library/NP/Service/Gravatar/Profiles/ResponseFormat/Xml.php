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
 * XML response format.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */
class NP_Service_Gravatar_Profiles_ResponseFormat_Xml
    extends NP_Service_Gravatar_Profiles_ResponseFormat_Abstract
    implements NP_Service_Gravatar_Profiles_ResponseFormat_ParserInterface
{
    protected $_id = 'xml';

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
           $xml = simplexml_load_string($body);
        }
        catch (Exception $ex) {
            require_once 'NP/Service/Gravatar/Profiles/ResponseFormat/Exception.php';
            throw new NP_Service_Gravatar_Profiles_ResponseFormat_Exception('Invalid XML response.');
        }

        if (!$xml->entry) { //Probably unexisting user is supplied.
            return $response;
        }
        
        $profileData = $this->_xmlToArray($xml->entry[0]);
        
        require_once 'NP/Service/Gravatar/Profiles/Profile.php';
        
        return new NP_Service_Gravatar_Profiles_Profile($profileData);
    }

    /**
     * Converts Gravatar XML response to array.
     *
     * @param SimpleXMLElement $xml
     * @return array
     */
    protected function _xmlToArray($simpleXmlObject)
    {
        if ($simpleXmlObject instanceof SimpleXMLElement) { //Converting to array.
            $simpleXmlObject = get_object_vars($simpleXmlObject);
        }
        
        if (is_array($simpleXmlObject)) { //Has children?
            $data = array();

            foreach ($simpleXmlObject as $key=>$value) {
                $data[$key] = $this->_xmlToArray($value);
            }

            return $data;
        }
        else {
            return trim((string)$simpleXmlObject);
        }
    }
}