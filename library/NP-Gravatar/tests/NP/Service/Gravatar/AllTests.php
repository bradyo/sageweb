<?php
/**
 * All NP_Service_Gravatar tests.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */

require_once dirname(__FILE__) . '/../../../TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'NP_Service_Gravatar_AllTests::main');
}

require_once 'NP/Service/Gravatar/Profiles/AllTests.php';
require_once 'NP/Service/Gravatar/XmlRpc/XmlRpcTest.php';

class NP_Service_Gravatar_AllTests
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
        $suite = new PHPUnit_Framework_TestSuite('NP - NP_Service_Gravatar');

        $suite->addTest(NP_Service_Gravatar_Profiles_AllTests::suite());
        $suite->addTestSuite(NP_Service_Gravatar_Profiles_XmlRpcTest);

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'NP_Service_Gravatar_AllTests::main') {
    NP_Service_Gravatar_AllTests::main();
}