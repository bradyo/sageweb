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
 * NP_Service_Gravatar_Profiles tests.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */

require_once dirname(__FILE__) . '/../../../../TestHelper.php';

require_once 'NP/Service/Gravatar/Profiles.php';

class NP_Service_Gravatar_Profiles_ProfilesTest extends PHPUnit_Framework_TestCase
{
    protected $_gravatarService;

    protected function setUp()  {
        $this->_gravatarService = new NP_Service_Gravatar_Profiles();
    }

    public function testPhpShouldBeResponseFormatAfterCreatingInstance()
    {
        $gravatarService = new NP_Service_Gravatar_Profiles();

        $this->assertTrue($gravatarService->getResponseFormat() instanceof NP_Service_Gravatar_Profiles_ResponseFormat_Php);
    }

    public function testSetDefaultResponseFormat()
    {
        require_once 'NP/Service/Gravatar/Profiles/ResponseFormat/Json.php';
        NP_Service_Gravatar_Profiles::setDefaultResponseFormat(new NP_Service_Gravatar_Profiles_ResponseFormat_Json());

        $this->assertTrue(NP_Service_Gravatar_Profiles::getDefaultResponseFormat() instanceof NP_Service_Gravatar_Profiles_ResponseFormat_Json);

        $gravatarService = new NP_Service_Gravatar_Profiles();
        $this->assertTrue($gravatarService->getResponseFormat() instanceof NP_Service_Gravatar_Profiles_ResponseFormat_Json);
    }

    public function testGetProfileInfoRawFlagSetToTrueShouldReturnResponseInstance()
    {
        $retval = $this->_gravatarService->getProfileInfo(GRAVATAR_ACCOUNT_EMAIL, true);

        $this->assertTrue($retval instanceof Zend_Http_Response);
    }

    public function testGetProfileDataWithPhpResponseFormatForceResponseRetval()
    {
        require_once 'NP/Service/Gravatar/Profiles/ResponseFormat/Php.php';
        $this->_gravatarService->setResponseFormat(new NP_Service_Gravatar_Profiles_ResponseFormat_Php());
        $retval = $this->_gravatarService->getProfileInfo(GRAVATAR_ACCOUNT_EMAIL, true);

        $this->assertTrue($retval instanceof Zend_Http_Response);

        $this->assertRegexp('/text\/plain/i', $retval->getHeader('Content-type'));
    }
    
    public function testGetProfileDataWithJsonResponseFormatForceResponseRetval()
    {
        require_once 'NP/Service/Gravatar/Profiles/ResponseFormat/Json.php';
        $this->_gravatarService->setResponseFormat(new NP_Service_Gravatar_Profiles_ResponseFormat_Json());
        $retval = $this->_gravatarService->getProfileInfo(GRAVATAR_ACCOUNT_EMAIL, true);

        $this->assertTrue($retval instanceof Zend_Http_Response);

        $this->assertRegexp('/application\/json/i', $retval->getHeader('Content-type'));
    }

    public function testGetProfileDataWithXmlResponseFormatForceResponseRetval()
    {
        require_once 'NP/Service/Gravatar/Profiles/ResponseFormat/Xml.php';
        $this->_gravatarService->setResponseFormat(new NP_Service_Gravatar_Profiles_ResponseFormat_Xml());
        $retval = $this->_gravatarService->getProfileInfo(GRAVATAR_ACCOUNT_EMAIL, true);

        $this->assertTrue($retval instanceof Zend_Http_Response);

        $this->assertRegexp('/application\/xml/i', $retval->getHeader('Content-type'));
    }

    public function testGetProfileDataWithQRCodeResponseFormat()
    {
        require_once 'NP/Service/Gravatar/Profiles/ResponseFormat/QRCode.php';
        $this->_gravatarService->setResponseFormat(new NP_Service_Gravatar_Profiles_ResponseFormat_QRCode());
        $retval = $this->_gravatarService->getProfileInfo(GRAVATAR_ACCOUNT_EMAIL);

        $this->assertTrue($retval instanceof Zend_Http_Response);

        //echo $retval->getHeadersAsString();
        $this->assertRegexp('/image\/png/i', $retval->getHeader('Content-type'));
    }

    public function testGetProfileDataWithVCardResponseFormat()
    {
        require_once 'NP/Service/Gravatar/Profiles/ResponseFormat/VCard.php';
        $this->_gravatarService->setResponseFormat(new NP_Service_Gravatar_Profiles_ResponseFormat_VCard());
        $retval = $this->_gravatarService->getProfileInfo(GRAVATAR_ACCOUNT_EMAIL);

        $this->assertTrue($retval instanceof Zend_Http_Response);

        $this->assertRegexp('/text\/directory/i', $retval->getHeader('Content-type'));
        $this->assertRegexp('/\.vcf/i', $retval->getHeader('Content-disposition'));
    }
}