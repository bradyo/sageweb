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
 * NP_Service_Gravatar_Profiles profile class tests.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */

require_once dirname(__FILE__) . '/../../../../../TestHelper.php';

require_once 'NP/Service/Gravatar/Profiles/Profile.php';

class NP_Service_Gravatar_Profiles_Profile_ProfileTest extends PHPUnit_Framework_TestCase
{
    protected $_profile;

    protected static $_testProfileData;

    public static function setUpBeforeClass()
    {
        self::$_testProfileData = include dirname(__FILE__) . '/_files/profile_data.php';
    }

    protected function setUp()
    {
        $this->_profile = new NP_Service_Gravatar_Profiles_Profile(self::$_testProfileData);
    }

    public function testAllFieldsAreSet()
    {
        $this->assertSame($this->_profile->id, self::$_testProfileData['id']); //__get
        $this->assertTrue($this->_profile->getProfileUrl() instanceof Zend_Uri_Http); //Must be normalized to Zend_Uri_Http.
        $this->assertSame($this->_profile->getPreferredUsername(), self::$_testProfileData['preferredUsername']);
        $this->assertTrue($this->_profile->thumbnailUrl instanceof Zend_Uri_Http);

        $this->assertTrue(is_array($this->_profile->getPhotos()));
        $this->assertEquals(count($this->_profile->photos), count(self::$_testProfileData['photos']));
        $this->assertTrue($this->_profile->photos[0] instanceof NP_Service_Gravatar_Profiles_Profile_Photo);
        
        $this->assertTrue($this->_profile->getProfileBackground() instanceof NP_Service_Gravatar_Profiles_Profile_Background);

        $this->assertTrue($this->_profile->name instanceof NP_Service_Gravatar_Profiles_Profile_Name);

        $this->assertSame($this->_profile->displayName, self::$_testProfileData['displayName']);
        $this->assertSame($this->_profile->aboutMe, self::$_testProfileData['aboutMe']);
        $this->assertSame($this->_profile->currentLocation, self::$_testProfileData['currentLocation']);

        $this->assertTrue(is_array($this->_profile->getEmails()));
        $this->assertEquals(count($this->_profile->emails), count(self::$_testProfileData['emails']));
        $this->assertTrue($this->_profile->emails[0] instanceof NP_Service_Gravatar_Profiles_Profile_Email);

        $this->assertTrue(is_array($this->_profile->getIms()));
        $this->assertEquals(count($this->_profile->ims), count(self::$_testProfileData['ims']));
        $this->assertTrue($this->_profile->ims[0] instanceof NP_Service_Gravatar_Profiles_Profile_Im);

        $this->assertTrue(is_array($this->_profile->getAccounts()));
        $this->assertEquals(count($this->_profile->accounts), count(self::$_testProfileData['accounts']));
        $this->assertTrue($this->_profile->accounts[0] instanceof NP_Service_Gravatar_Profiles_Profile_Account);

        $this->assertTrue(is_array($this->_profile->getUrls()));
        $this->assertEquals(count($this->_profile->urls), count(self::$_testProfileData['urls']));
        $this->assertTrue($this->_profile->urls[0] instanceof NP_Service_Gravatar_Profiles_Profile_Url);
    }

    public function testSettingPhotos()
    {
        $photos = array('value'=>'http://www.blogger.com/profile/1111'); //This should be turned into array.
        $this->_profile->setPhotos($photos);
        $profilePhotos = $this->_profile->photos;
        $this->assertTrue($profilePhotos[0]->value instanceof Zend_Uri_Http);

        $photos = array(new NP_Service_Gravatar_Profiles_Profile_Photo(array('value'=>'http://www.blogger.com/profile/2222'))); //This should be as is.
        $this->_profile->setPhotos($photos);
        $profilePhotos = $this->_profile->getPhotos();
        $this->assertTrue($profilePhotos[0] instanceof NP_Service_Gravatar_Profiles_Profile_Photo);
    }

    public function testSettingPhoneNumbers()
    {
        $phoneNumbers = array('type'=>'home', 'value'=>1234567); //This should be turned into array.
        $this->_profile->setPhoneNumbers($phoneNumbers);
        $profilePhoneNumbers = $this->_profile->phoneNumbers;
        $this->assertSame($profilePhoneNumbers[0]->type, $phoneNumbers['type']);
    }

    public function testSettingEmails()
    {
        $emails = array(
            array('value'=>'foo@bar.com', 'primary'=>true),
            new NP_Service_Gravatar_Profiles_Profile_Email(array('value'=>'foo.bar@foobar.com', 'primary'=>false))
        );
        $this->_profile->setEmails($emails);
        $profileEmails = $this->_profile->emails;
        $this->assertSame($profileEmails[0]->value, $emails[0]['value']);
        $this->assertSame($profileEmails[1], $emails[1]);
    }
}