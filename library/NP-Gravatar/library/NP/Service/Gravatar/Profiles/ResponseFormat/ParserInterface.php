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
 * Interface that should be implemented by response formats that
 * can parse response body and generete NP_Gravatar_Profile
 * instance.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */
interface NP_Service_Gravatar_Profiles_ResponseFormat_ParserInterface
{
    /**
     * Parses response body and generates NP_Gravatar_Profile
     * instance. If for some reason response could not be
     * converted to NP_Gravatar_Profile, $response will be
     * returned.
     *
     * @param Zend_Http_Response $response
     * @return NP_Gravatar_Profile|Zend_Http_Response
     */
    public function profileFromHttpResponse(Zend_Http_Response $response);
}