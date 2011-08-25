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
 * NP_Service_Gravatar_Profiles vCard response formats tests.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */

require_once dirname(__FILE__) . '/../../../../../TestHelper.php';

require_once 'NP/Service/Gravatar/Profiles/ResponseFormat/VCard.php';

class NP_Service_Gravatar_Profiles_ResponseFormat_VCardTest extends PHPUnit_Framework_TestCase
{
    protected $_vcardResponseFormat;

    protected function setUp()
    {
        $this->_vcardResponseFormat = new NP_Service_Gravatar_Profiles_ResponseFormat_VCard();
    }

    public function testVcfResponseFormatId()
    {
        $this->assertSame($this->_vcardResponseFormat->getResponseFormatId(), 'vcf');
    }
}