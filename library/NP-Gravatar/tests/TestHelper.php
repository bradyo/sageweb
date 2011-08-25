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
 * Setting up testing environment.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */

/*
 * Include PHPUnit dependencies.
 */
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Framework/IncompleteTestError.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/Runner/Version.php';
require_once 'PHPUnit/TextUI/TestRunner.php';
require_once 'PHPUnit/Util/Filter.php';

error_reporting(E_ALL ^ E_NOTICE ^ E_USER_NOTICE);
ini_set('display_errors', 1);

$npRoot = realpath(dirname(dirname(__FILE__)));
$npLibrary = "$npRoot/library";
$npTests = "$npRoot/tests";

/*
 * Load the user-defined test configuration file, if it exists; otherwise, load
 * the default configuration.
 */
if (is_readable($npTests . DIRECTORY_SEPARATOR . 'TestConfiguration.php')) {
    require_once $npTests . DIRECTORY_SEPARATOR . 'TestConfiguration.php';
} else {
    require_once $npTests . DIRECTORY_SEPARATOR . 'TestConfiguration.php.dist';
}

$path = array(
    ZEND_FRAMEWORK_PATH,
	$npLibrary,
    $npTests,
    get_include_path()
    );
set_include_path(implode(PATH_SEPARATOR, $path));

/*
 * Unset global variables that are no longer needed.
 */
unset($npRoot, $npLibrary, $npTests);