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
 * NP_Service_Gravatar_Profiles QR Code response formats tests.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */

require_once dirname(__FILE__) . '/../../../../../TestHelper.php';

require_once 'NP/Service/Gravatar/Profiles/ResponseFormat/QRCode.php';

class NP_Service_Gravatar_Profiles_ResponseFormat_QRCodeTest extends PHPUnit_Framework_TestCase
{
    protected $_qrcodeResponseFormat;

    protected function setUp()
    {
        $this->_qrcodeResponseFormat = new NP_Service_Gravatar_Profiles_ResponseFormat_QRCode();
    }

    public function testQRCodeResponseFormatId()
    {
        $this->assertSame($this->_qrcodeResponseFormat->getResponseFormatId(), 'qr');
    }
}