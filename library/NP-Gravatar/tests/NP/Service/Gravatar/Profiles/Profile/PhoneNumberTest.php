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
 * NP_Service_Gravatar_Profiles PhoneNumber class tests.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */

require_once dirname(__FILE__) . '/../../../../../TestHelper.php';

require_once 'NP/Service/Gravatar/Profiles/Profile/PhoneNumber.php';

class NP_Service_Gravatar_Profiles_Profile_PhoneNumberTest extends PHPUnit_Framework_TestCase
{
    protected $_phoneNumberData = array(
        array('type'=>'mobile', 'value'=>1234567)
    );

    public function testConstructor()
    {
        $phoneNumber = new NP_Service_Gravatar_Profiles_Profile_PhoneNumber($this->_phoneNumberData);

        $this->assertSame($phoneNumber->type, $this->_phoneNumberData['type']); //__get
        $this->assertSame($phoneNumber->getValue(), $this->_phoneNumberData['value']);
    }
}