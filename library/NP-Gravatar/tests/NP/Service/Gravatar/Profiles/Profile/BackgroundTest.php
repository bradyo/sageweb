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
 * NP_Service_Gravatar_Profiles Background class tests.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */

require_once dirname(__FILE__) . '/../../../../../TestHelper.php';

require_once 'NP/Service/Gravatar/Profiles/Profile/Background.php';

class NP_Service_Gravatar_Profiles_Profile_BackgroundTest extends PHPUnit_Framework_TestCase
{
    protected $_profileBackground;

    protected $_backgroundData = array(
        'color'=>'#d1d1d1',
        'position'=>'left',
        'repeat'=>'repeat',
        'url'=>'http://www.gravatar.com/bg/1/1111'
    );

    protected function setUp()
    {
        $this->_profileBackground = new NP_Service_Gravatar_Profiles_Profile_Background();
    }

    public function testConstructor()
    {
        $profileBackground = new NP_Service_Gravatar_Profiles_Profile_Background($this->_backgroundData);

        $this->assertSame($profileBackground->color, $this->_backgroundData['color']); //__get
        $this->assertSame($profileBackground->getPosition(), $this->_backgroundData['position']);
        $this->assertSame($profileBackground->repeat, $this->_backgroundData['repeat']);
        $this->assertTrue($profileBackground->getUrl() instanceof Zend_Uri_Http);
    }

    public function testInvalidUrlShouldThrowException()
    {
        $this->setExpectedException('NP_Service_Gravatar_Exception');

        $this->_profileBackground->setUrl('invalid');
    }
}