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
 * NP_Service_Gravatar_Profiles Im class tests.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */

require_once dirname(__FILE__) . '/../../../../../TestHelper.php';

require_once 'NP/Service/Gravatar/Profiles/Profile/Im.php';

class NP_Service_Gravatar_Profiles_Profile_ImTest extends PHPUnit_Framework_TestCase
{
    protected $_imData = array('type'=>'gtalk', 'value'=>'foo.bar@gmail.com');

    public function testConstructor()
    {
        $im = new NP_Service_Gravatar_Profiles_Profile_Im($this->_imData);

        $this->assertSame($im->type, $this->_imData['type']); //__get
        $this->assertSame($im->getValue(), $this->_imData['value']);
    }
}