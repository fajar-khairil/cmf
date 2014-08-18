<?php
/**
 *
 *  Bootstraping the App
 *
 *  @license : MIT 
 *  @author  : Fajar Khairil
 */

define('APC_PRESENT',extension_loaded('apc') AND (boolean)ini_get('apc.enabled'));

date_default_timezone_set('Asia/Jakarta');

require 'vendor/autoload.php';

Application::$ENGINE_PATH = __DIR__;
Application::$BACKEND_URI = 'administrator';
Application::$BASE_URL = 'http://unikacmf.dev/';

//environtment detection by machine name
$environtments = array(
    'local' => array(
        'fajardev'
    ),
    'staging'   =>  array(
        'staging-machine'
    )
);

Application::detectEnvirontment( $environtments );