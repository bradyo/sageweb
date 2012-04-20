<?php
require_once('include.php');

// set up application path based on server settings
define('APPLICATION_ENV', 'production');
define('BASE_PATH', realpath(dirname(__FILE__) . '/..'));
define('APPLICATION_PATH', BASE_PATH . '/application');
define('DATA_PATH', BASE_PATH . '/data');

// Ensure libraries are on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(BASE_PATH . '/library'),
    realpath(BASE_PATH . '/library/ZendFramework-1.11.11-minimal/library'),
    realpath(BASE_PATH . '/library/Doctrine-1.2.3'),
    realpath(BASE_PATH . '/library/HtmlPurifier'),
    realpath(BASE_PATH . '/library/NP-Gravatar/library'),
    get_include_path(),
)));

// clean session when script closed
function shutdown()
{
     require_once 'Zend/Session.php';
     Zend_Session::writeClose(true);
}
register_shutdown_function('shutdown');

// boostrap and run application
require_once 'Zend/Application.php';
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap();
$application->run();
