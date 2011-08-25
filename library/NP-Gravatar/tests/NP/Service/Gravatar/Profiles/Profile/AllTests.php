<?php
/**
 * All NP_Service_Gravatar_Profiles_Profile tests.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */

require_once dirname(__FILE__) . '/../../../../../TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'NP_Service_Gravatar_Profiles_Profile_AllTests::main');
}

require_once 'NP/Service/Gravatar/Profiles/Profile/ProfileTest.php';
require_once 'NP/Service/Gravatar/Profiles/Profile/AccountTest.php';
require_once 'NP/Service/Gravatar/Profiles/Profile/BackgroundTest.php';
require_once 'NP/Service/Gravatar/Profiles/Profile/EmailTest.php';
require_once 'NP/Service/Gravatar/Profiles/Profile/ImTest.php';
require_once 'NP/Service/Gravatar/Profiles/Profile/NameTest.php';
require_once 'NP/Service/Gravatar/Profiles/Profile/PhoneNumberTest.php';
require_once 'NP/Service/Gravatar/Profiles/Profile/PhotoTest.php';
require_once 'NP/Service/Gravatar/Profiles/Profile/UrlTest.php';

class NP_Service_Gravatar_Profiles_Profile_AllTests
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
        $suite = new PHPUnit_Framework_TestSuite('NP - NP_Service_Gravatar_Profiles_Profile');

        $suite->addTestSuite('NP_Service_Gravatar_Profiles_Profile_ProfileTest');
        $suite->addTestSuite('NP_Service_Gravatar_Profiles_Profile_AccountTest');
        $suite->addTestSuite('NP_Service_Gravatar_Profiles_Profile_BackgroundTest');
        $suite->addTestSuite('NP_Service_Gravatar_Profiles_Profile_EmailTest');
        $suite->addTestSuite('NP_Service_Gravatar_Profiles_Profile_ImTest');
        $suite->addTestSuite('NP_Service_Gravatar_Profiles_Profile_NameTest');
        $suite->addTestSuite('NP_Service_Gravatar_Profiles_Profile_PhoneNumberTest');
        $suite->addTestSuite('NP_Service_Gravatar_Profiles_Profile_PhotoTest');
        $suite->addTestSuite('NP_Service_Gravatar_Profiles_Profile_UrlTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'NP_Service_Gravatar_Profiles_Profile_AllTests::main') {
    NP_Service_Gravatar_Profiles_Profile_AllTests::main();
}