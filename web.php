<?php
/**
 *
 *  Bootstraping the Web App
 *
 *  @license : MIT 
 *  @author  : Fajar Khairil
 */

require_once 'bootstrap.php';

Application::$BACKEND_URI = 'administrator';
Application::$BASE_URL = 'http://unikacmf.dev/';

$app = Application::instance();

$app->error(function($e){
	print_r(get_class( $e ));
});

require '../routes.php';

$app->run();