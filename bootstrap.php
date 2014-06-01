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

\Unika\Bag::$ENGINE_PATH = __DIR__;

//environtment detection source
$environtments = array(
    'local' => array(
        'fajardev'
    ),
    'staging'   =>  array(
        'staging-machine'
    )
);

\Unika\Application::detectEnvirontment( $environtments );