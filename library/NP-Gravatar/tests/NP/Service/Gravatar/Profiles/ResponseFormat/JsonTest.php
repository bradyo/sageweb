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
 * NP_Service_Gravatar_Profiles JSON response formats tests.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */

require_once dirname(__FILE__) . '/../../../../../TestHelper.php';

require_once 'NP/Service/Gravatar/Profiles/ResponseFormat/Json.php';
require_once 'Zend/Http/Response.php';

class NP_Service_Gravatar_Profiles_ResponseFormat_JsonTest extends PHPUnit_Framework_TestCase
{
    protected $_jsonResponseFormat;

    protected function setUp()
    {
        $this->_jsonResponseFormat = new NP_Service_Gravatar_Profiles_ResponseFormat_Json();
    }

    public function testJsonResponseFormatId()
    {
        $this->assertSame($this->_jsonResponseFormat->getResponseFormatId(), 'json');
    }

    public function testProfileFromHttpResponseInvalidResponseBodyShouldThrowException()
    {
        $this->setExpectedException('NP_Service_Gravatar_Profiles_ResponseFormat_Exception');

        $profile = $this->_jsonResponseFormat->profileFromHttpResponse(
            new Zend_Http_Response(
                    200,
                    array('Content-Type' => 'application/json'),
                    'invalid'
           )
        );
    }

    public function testProfileFromHttpResponseNoEntryInResponseBodyShouldReturnHttpResponse()
    {
        $retval = $this->_jsonResponseFormat->profileFromHttpResponse(
            new Zend_Http_Response(
                    200,
                    array('Content-Type' => 'application/json'),
                    '{"foo":"bar"}'
           )
        );

        $this->assertTrue($retval instanceof Zend_Http_Response);
    }

    public function testProfileFromHttpResponseShouldReturnProfile()
    {
        $profile = $this->_jsonResponseFormat->profileFromHttpResponse(
            new Zend_Http_Response(
                    200,
                    array('Content-Type' => 'application/json'),
                    file_get_contents(dirname(__FILE__) . '/_files/json_response.json')
           )
        );
        
        $this->assertTrue($profile instanceof NP_Service_Gravatar_Profiles_Profile);
    }
}