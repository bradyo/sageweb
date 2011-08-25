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
 * NP_View_Helper_Gravatar tests.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */

require_once dirname(__FILE__) . '/../../../TestHelper.php';

require_once 'NP/View/Helper/Gravatar.php';

class NP_View_Helper_GravatarTest extends PHPUnit_Framework_TestCase
{
    protected $_gravatarViewHelper;
    
    protected function setUp()  {
        $this->_gravatarViewHelper = new NP_View_Helper_Gravatar();
    }

    public function testNoOptionsShouldBeIgnored()
    {
        $output = $this->_gravatarViewHelper->gravatar('foo@bar.com');
        
        $this->assertRegexp('/^' . preg_quote(NP_View_Helper_Gravatar::AVATAR_SERVER, '/') . '\/[a-z0-9]+$/', $output);
    }

    public function testWithSuppliedOptions()
    {
        $output = $this->_gravatarViewHelper->gravatar('foo@bar.com', array('s'=>200, 'rating'=>'pg'));

        $this->assertRegexp('/s\=200/', $output);
        $this->assertRegexp('/rating\=pg/', $output);
    }

    public function testWithSuppliedOptionsDuplicatesShouldBeFilteredAliasedKeys()
    {
        $output = $this->_gravatarViewHelper->gravatar('foo@bar.com', array('s'=>200, 'rating'=>'g', 'd'=>'404', 'default'=>'identicon'));
        
        $this->assertRegexp('/\?s\=200\&amp;rating\=g\&amp;default=identicon$/', $output);
    }

    public function testWithSuppliedExtension()
    {
        $output = $this->_gravatarViewHelper->gravatar('foo@bar.com', array('s'=>200), 'jpg');

        $this->assertRegexp('/\.jpg\?s\=200$/', $output);
    }
}