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
 * NP_Service_Gravatar_Profiles XML response formats tests.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */

require_once dirname(__FILE__) . '/../../../../../TestHelper.php';

require_once 'NP/Service/Gravatar/Profiles/ResponseFormat/Xml.php';
require_once 'Zend/Http/Response.php';

class NP_Service_Gravatar_Profiles_ResponseFormat_XmlTest extends PHPUnit_Framework_TestCase
{
    protected $_xmlResponseFormat;

    protected function setUp()
    {
        $this->_xmlResponseFormat = new NP_Service_Gravatar_Profiles_ResponseFormat_Xml();
    }

    public function testXmlResponseFormatId()
    {
        $this->assertSame($this->_xmlResponseFormat->getResponseFormatId(), 'xml');
    }

    public function testProfileFromHttpResponseInvalidResponseBodyShouldThrowException()
    {
        $this->setExpectedException('NP_Service_Gravatar_Profiles_ResponseFormat_Exception');

        $profile = $this->_xmlResponseFormat->profileFromHttpResponse(
            new Zend_Http_Response(
                    200,
                    array('Content-Type' => 'application/xml'),
                    'invalid'
           )
        );
    }

    public function testXmlResponseFormatProfileFromHttpResponse()
    {
        $profile = $this->_xmlResponseFormat->profileFromHttpResponse(
            new Zend_Http_Response(
                    200,
                    array('Content-Type' => 'application/xml'),
                    file_get_contents(dirname(__FILE__) . '/_files/xml_response.xml')
           )
        );
        
        $this->assertTrue($profile instanceof NP_Service_Gravatar_Profiles_Profile);
    }
}