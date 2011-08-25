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
 * NP_Service_Gravatar_XmlRpc tests.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */

require_once dirname(__FILE__) . '/../../../../TestHelper.php';

require_once 'NP/Service/Gravatar/XmlRpc.php';

class NP_Service_Gravatar_Profiles_XmlRpcTest extends PHPUnit_Framework_TestCase
{
    protected $_gravatarXmlRpc;

    protected static $_currentImage;
    
    protected static $_savedImages = array();

    protected $_useMethodPassed = false;
    
    protected function setUp()  {
        $this->_gravatarXmlRpc = new NP_Service_Gravatar_XmlRpc(GRAVATAR_API_KEY, GRAVATAR_ACCOUNT_EMAIL);
    }

    public static function tearDownAfterClass()
    {
        $gravatarXmlRpc = new NP_Service_Gravatar_XmlRpc(GRAVATAR_API_KEY, GRAVATAR_ACCOUNT_EMAIL);

        foreach (self::$_savedImages as $image) {
            $gravatarXmlRpc->deleteUserimage($image);
        }

        $gravatarXmlRpc->useUserimage(self::$_currentImage, GRAVATAR_ACCOUNT_EMAIL);
    }

    public function testSettingApiKeyAndEmailFromTheConstructor()
    {
        $gravatarXmlRpc = new NP_Service_Gravatar_XmlRpc('foo123', 'foo@bar.com');

        $this->assertSame($gravatarXmlRpc->getApiKey(), 'foo123');
        $this->assertSame($gravatarXmlRpc->getEmail(), 'foo@bar.com');
    }

    public function testTestingMethod()
    {
        $result = $this->_gravatarXmlRpc->test();

        $this->assertTrue(array_key_exists('apikey', $result));
        $this->assertTrue(array_key_exists('response', $result));
    }

    public function testExistsMethod()
    {
        $this->assertTrue((bool)current($this->_gravatarXmlRpc->exists(GRAVATAR_ACCOUNT_EMAIL)));
    }

    public function testExistsMethodMultipleEmails()
    {
        $result = $this->_gravatarXmlRpc->exists(array(GRAVATAR_ACCOUNT_EMAIL, 'invalid'));

        $values = array_values($result);
        $this->assertTrue((bool)$values[0]);
        $this->assertFalse((bool)$values[1]);
    }

    public function testAdressesMethod()
    {
        $result = $this->_gravatarXmlRpc->addresses();

        $address = $result[0]; //Valid account must have at least one email address.
        $this->assertTrue($address instanceof NP_Service_Gravatar_XmlRpc_UserEmail);
        $this->assertTrue($address->getImage() instanceof NP_Service_Gravatar_XmlRpc_UserImage);

        //Saving current image, so it can be reseted in tearDown(), after testing.
        self::$_currentImage = $address->getImage()->getId();
    }

    public function testUserImagesMethod()
    {
        $result = $this->_gravatarXmlRpc->userImages();

        $image = $result[0]; //Valid account must have at least one image.
        $this->assertTrue($image instanceof NP_Service_Gravatar_XmlRpc_UserImage);
        $this->assertTrue($image->getUrl() instanceof Zend_Uri_Http);
        $this->assertTrue(array_key_exists($image->getRating(), NP_Service_Gravatar_XmlRpc::getValidRatings()));
    }

    public function testSaveDataMethodShouldThrowExceptionOnInvalidRating()
    {
        $this->setExpectedException('NP_Service_Gravatar_XmlRpc_Exception');

        $this->_gravatarXmlRpc->saveData('/path/to/image.jpg', 77);
    }

    public function testSaveDataMethodPath()
    {
        $result = $this->_gravatarXmlRpc->saveData(dirname(__FILE__) . '/_files/deafultGravatarThumb.png', NP_Service_Gravatar_XmlRpc::PG_RATED);

        if ($result) {
            self::$_savedImages[] = $result;
        }
    }

    public function testSaveDataMethodContent()
    {
        $result = $this->_gravatarXmlRpc->saveData(file_get_contents(dirname(__FILE__) . '/_files/default_gravatar.gif'), NP_Service_Gravatar_XmlRpc::R_RATED);

        if ($result) {
            self::$_savedImages[] = $result;
        }
    }

    public function testSaveUrlMethodShouldThrowExceptionOnInvalidRating()
    {
        $this->setExpectedException('NP_Service_Gravatar_XmlRpc_Exception');

        $this->_gravatarXmlRpc->saveUrl('www.example.com/path/to/image.jpg', 77);
    }

    public function testSaveUrlMethod()
    {
        $result = $this->_gravatarXmlRpc->saveUrl('http://buildinternet.com/wp-content/uploads/gravatar-change-thumb.jpg', NP_Service_Gravatar_XmlRpc::G_RATED);

        if ($result) {
            self::$_savedImages[] = $result;
        }
    }

    public function testUseUserImageMethod()
    {
        if (count(self::$_savedImages) > 0) {
            $result = $this->_gravatarXmlRpc->useUserImage(self::$_savedImages[0], GRAVATAR_ACCOUNT_EMAIL);
            
            if ((bool)current($result) == true) {
                $this->_useMethodPassed = true;
            }
        }
    }

    public function testRemoveImageMethod()
    {
        if ($this->_useMethodPassed == true) {
            $this->_gravatarXmlRpc->removeImage(GRAVATAR_ACCOUNT_EMAIL);
            unset(self::$_savedImages[0]);
        }
    }

    public function testDeleteUserImageMethod()
    {
        if (count(self::$_savedImages) > 0) {
            $result = $this->_gravatarXmlRpc->deleteUserImage(current(self::$_savedImages));
            unset(self::$_savedImages[key(self::$_savedImages)]);
        }
    }
}