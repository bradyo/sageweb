<?php
/**
 * All NP_Service_Gravatar_Profiles tests.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */

require_once dirname(__FILE__) . '/../../../../TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'NP_Service_Gravatar_Profiles_AllTests::main');
}

require_once 'NP/Service/Gravatar/Profiles/ProfilesTest.php';
require_once 'NP/Service/Gravatar/Profiles/ResponseFormat/AllTests.php';
require_once 'NP/Service/Gravatar/Profiles/Profile/AllTests.php';

class NP_Service_Gravatar_Profiles_AllTests
{
    /**
     * Runs this test suite.
     *
     * @return void
     */
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    /**
     * Creates and returns this test suite.
     *
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('NP - NP_Service_Gravatar_Profiles');

        $suite->addTestSuite('NP_Service_Gravatar_Profiles_ProfilesTest');
        $suite->addTest(NP_Service_Gravatar_Profiles_ResponseFormat_AllTests::suite());
        $suite->addTest(NP_Service_Gravatar_Profiles_Profile_AllTests::suite());

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'NP_Service_Gravatar_Profiles_AllTests::main') {
    NP_Service_Gravatar_Profiles_AllTests::main();
}