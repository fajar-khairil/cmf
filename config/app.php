<?php
return array(
	'debug' 			=> False,
    //use to encrypt cookie and other security related min character 8
    'secret_key'        => 'HfhdsknliUIHH89j9B3hnKNKJB',
    'backend_uri'       => \Unika\Bag::$BACKEND_URI,
	'base_url'			=> \Unika\Bag::$BASE_URL,
	/**
     *  
     *  name of algorithm to sign and unsign sensitive data
     *  valid value http://www.php.net/manual/en/function.hash-algos.php
     */
	'sign_algo'			=> 'ripemd160',
    'charset' 			=> 'utf-8',
    'locale' 			=> 'en',  
    'tmp_dir'			=> \Unika\Bag::$ENGINE_PATH.DIRECTORY_SEPARATOR.'tmp',
    'log_dir'           => \Unika\Bag::$ENGINE_PATH.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'logs'
);