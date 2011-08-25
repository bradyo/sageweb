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
 * NP_Service_Gravatar_Profiles Account class tests.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */

require_once dirname(__FILE__) . '/../../../../../TestHelper.php';

require_once 'NP/Service/Gravatar/Profiles/Profile/Account.php';

class NP_Service_Gravatar_Profiles_Profile_AccountTest extends PHPUnit_Framework_TestCase
{
    protected $_profileAccount;

    protected $_acountData = array(
        'domain'=>'facebook.com',
        'username'=>'foobar',
        'display'=>'foobar',
        'url'=>'http://www.facebook.com/foobar',
        'verified'=>true,
        'shortname'=>'facebook'
    );

    protected function setUp()
    {
        $this->_profileAccount = new NP_Service_Gravatar_Profiles_Profile_Account();
    }

    public function testConstructor()
    {
        $account = new NP_Service_Gravatar_Profiles_Profile_Account($this->_acountData);

        $this->assertSame($account->domain, $this->_acountData['domain']); //__get
        $this->assertSame($account->getUsername(), $this->_acountData['username']);
        $this->assertSame($account->display, $this->_acountData['display']);
        $this->assertTrue($account->getUrl() instanceof Zend_Uri_Http);
        $this->assertTrue($account->verified);
        $this->assertSame($account->getShortname(), $this->_acountData['shortname']);
    }

    public function testInvalidUrlShouldThrowException()
    {
        $this->setExpectedException('NP_Service_Gravatar_Exception');

        $this->_profileAccount->setUrl('invalid');
    }

    public function testSettingVerifiedAsStringShouldBeNormalizedToBool()
    {
         $this->_profileAccount->verified = 'true'; //__set

         $this->assertTrue($this->_profileAccount->getVerified());
         $this->assertTrue(is_bool($this->_profileAccount->getVerified()));
    }
}