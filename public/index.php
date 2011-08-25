<?php
require_once('include.php');

// set up application path based on server settings
define('APPLICATION_ENV', 'development');
define('BASE_PATH', realpath(dirname(__FILE__) . '/..'));
define('APPLICATION_PATH', BASE_PATH . '/application');
define('DATA_PATH', BASE_PATH . '/data');

// Ensure libraries are on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(BASE_PATH . '/library'),
    realpath(BASE_PATH . '/library/Doctrine-1.2.4'),
    realpath(BASE_PATH . '/library/HtmlPurifier'),
    realpath(BASE_PATH . '/library/NP-Gravatar/library'),
    get_include_path(),
)));

// boostrap and run application
require_once 'Zend/Application.php';
$application = new Zend_Application(
    APPLICATION_ENV, 
    APPLICATION_PATH . '/configs/application.ini'
);
$application->setBootstrap(APPLICATION_PATH . '/Bootstrap.php', 'Bootstrap');
$application->bootstrap();
$application->run();
