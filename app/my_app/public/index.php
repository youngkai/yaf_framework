<?php

define('APPLICATION_PATH', dirname(dirname(__FILE__)));
$application = new Yaf\Application( APPLICATION_PATH . "/conf/application.ini");
define('BOOTSTRAP_PATH', APPLICATION_PATH . DIRECTORY_SEPARATOR . 'BootstrapWebApi.php');
Yaf\Loader::import(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'Init.php');
$application->getDispatcher()->catchException(true);
$application->getDispatcher()->throwException(false);
$application->getDispatcher()->setErrorHandler(['Errors','errorHandler']);
$application->bootstrap()->run();
?>
