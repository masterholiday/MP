<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application/'));
// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    realpath(APPLICATION_PATH). '/controllers',
    realpath(APPLICATION_PATH . '/Obj'),
    realpath(APPLICATION_PATH),
    get_include_path(),
)));
if (isset($_POST['sessid'])) @session_id($_POST['sessid']);

/** Zend_Application */
//require_once 'Loginza/LoginzaAPI.class.php';

//require_once 'Loginza/LoginzaUserProfile.class.php';

require_once 'Zend/Application.php';


require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->setFallbackAutoloader(true);
// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()
            ->run();
