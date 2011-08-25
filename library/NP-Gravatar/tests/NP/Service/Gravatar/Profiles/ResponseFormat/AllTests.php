<?php
/**
 * All NP_Service_Gravatar_Profiles_ResponseFormat tests.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */

require_once dirname(__FILE__) . '/../../../../../TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'NP_Service_Gravatar_Profiles_ResponseFormat_AllTests::main');
}

require_once 'NP/Service/Gravatar/Profiles/ResponseFormat/JsonTest.php';
require_once 'NP/Service/Gravatar/Profiles/ResponseFormat/PhpTest.php';
require_once 'NP/Service/Gravatar/Profiles/ResponseFormat/QRCodeTest.php';
require_once 'NP/Service/Gravatar/Profiles/ResponseFormat/VCardTest.php';
require_once 'NP/Service/Gravatar/Profiles/ResponseFormat/XmlTest.php';

class NP_Service_Gravatar_Profiles_ResponseFormat_AllTests
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
        $suite = new PHPUnit_Framework_TestSuite('NP - NP_Service_Gravatar_Profiles_ResponseFormat');

        $suite->addTestSuite('NP_Service_Gravatar_Profiles_ResponseFormat_JsonTest');
        $suite->addTestSuite('NP_Service_Gravatar_Profiles_ResponseFormat_PhpTest');
        $suite->addTestSuite('NP_Service_Gravatar_Profiles_ResponseFormat_QRCodeTest');
        $suite->addTestSuite('NP_Service_Gravatar_Profiles_ResponseFormat_VCardTest');
        $suite->addTestSuite('NP_Service_Gravatar_Profiles_ResponseFormat_XmlTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'NP_Service_Gravatar_Profiles_ResponseFormat_AllTests::main') {
    NP_Service_Gravatar_Profiles_ResponseFormat_AllTests::main();
}