<?php
	ob_start();
	
	session_start();
	
	define('ANAX_INSTALL_PATH', realpath(__DIR__ . '/../../dahe-mvc/') . '/');
	define('ANAX_APP_PATH', ANAX_INSTALL_PATH . 'app/');

	include(ANAX_APP_PATH . 'config/autoloader.php');
	include(ANAX_INSTALL_PATH . 'src/functions.php');
?>

