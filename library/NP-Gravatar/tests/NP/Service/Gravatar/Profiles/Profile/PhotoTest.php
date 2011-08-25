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
 * NP_Service_Gravatar_Profiles Photo class tests.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */

require_once dirname(__FILE__) . '/../../../../../TestHelper.php';

require_once 'NP/Service/Gravatar/Profiles/Profile/Photo.php';

class NP_Service_Gravatar_Profiles_Profile_PhotoTest extends PHPUnit_Framework_TestCase
{
    protected $_profilePhoto;

    protected $_validUrl = 'http://www.gravatar.com/userimage/1/1111';

    protected function setUp()
    {
        $this->_profilePhoto = new NP_Service_Gravatar_Profiles_Profile_Photo();
    }

    public function testConstructor()
    {
        $photo = new NP_Service_Gravatar_Profiles_Profile_Photo(array(
                'value'=>$this->_validUrl
            )
        );

        $this->assertTrue($photo->getType() == null);
        $this->assertTrue($photo->value instanceof Zend_Uri_Http); //__get
    }

    public function testInvalidValueUrlShouldThrowException()
    {
        $this->setExpectedException('NP_Service_Gravatar_Exception');

        $this->_profilePhoto->setValue('invalid');
    }

    public function testSettingType()
    {
         $this->_profilePhoto->type = 'thumbnail'; //__set
         
         $this->assertSame($this->_profilePhoto->getType(), 'thumbnail');
    }

    public function testSettingValueShouldBeNormalizedToZendUriHttp()
    {
        $this->_profilePhoto->setValue($this->_validUrl);

        $this->assertTrue($this->_profilePhoto->value instanceof Zend_Uri_Http);
    }
}