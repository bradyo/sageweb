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
 * NP_Service_Gravatar_Profiles Name class tests.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */

require_once dirname(__FILE__) . '/../../../../../TestHelper.php';

require_once 'NP/Service/Gravatar/Profiles/Profile/Name.php';

class NP_Service_Gravatar_Profiles_Profile_NameTest extends PHPUnit_Framework_TestCase
{
    protected $_nameData = array(
        'givenName'=>'Foo',
        'familyName'=>'Bar',
        'formatted'=>'Foo Bar'
    );

    public function testConstructor()
    {
        $name = new NP_Service_Gravatar_Profiles_Profile_Name($this->_nameData);

        $this->assertSame($name->givenName, $this->_nameData['givenName']); //__get
        $this->assertSame($name->getFamilyName(), $this->_nameData['familyName']);
        $this->assertSame($name->getFormatted(), $this->_nameData['formatted']);
    }
}