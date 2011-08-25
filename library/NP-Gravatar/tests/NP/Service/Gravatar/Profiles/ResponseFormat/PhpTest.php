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
 * NP_Service_Gravatar_Profiles PHP response formats tests.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */

require_once dirname(__FILE__) . '/../../../../../TestHelper.php';

require_once 'NP/Service/Gravatar/Profiles/ResponseFormat/Php.php';
require_once 'Zend/Http/Response.php';

class NP_Service_Gravatar_Profiles_ResponseFormat_PhpTest extends PHPUnit_Framework_TestCase
{
    protected $_phpResponseFormat;

    protected function setUp()
    {
        $this->_phpResponseFormat = new NP_Service_Gravatar_Profiles_ResponseFormat_Php();
    }

    public function testPhpResponseFormatId()
    {
        $this->assertSame($this->_phpResponseFormat->getResponseFormatId(), 'php');
    }

    public function testProfileFromHttpResponseInvalidResponseBodyShouldThrowException()
    {
        $this->setExpectedException('NP_Service_Gravatar_Profiles_ResponseFormat_Exception');

        $profile = $this->_phpResponseFormat->profileFromHttpResponse(
            new Zend_Http_Response(
                    200,
                    array('Content-Type' => 'text/plain'),
                    'invalid'
           )
        );
    }

    public function testProfileFromHttpResponseNoEntryInResponseBodyShouldReturnHttpResponse()
    {
        $retval = $this->_phpResponseFormat->profileFromHttpResponse(
            new Zend_Http_Response(
                    200,
                    array('Content-Type' => 'text/plain'),
                    's:14:"User not found";'
           )
        );

        $this->assertTrue($retval instanceof Zend_Http_Response);
    }

    public function testProfileFromHttpResponseShouldReturnProfile()
    {
        $profile = $this->_phpResponseFormat->profileFromHttpResponse(
            new Zend_Http_Response(
                    200,
                    array('Content-Type' => 'text/plain'),
                    file_get_contents(dirname(__FILE__) . '/_files/php_response')
           )
        );
        
        $this->assertTrue($profile instanceof NP_Service_Gravatar_Profiles_Profile);
    }
}