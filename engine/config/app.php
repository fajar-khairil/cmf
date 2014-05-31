<?php
return array(
	'debug' 			=> False,
    'backend_uri'       => \Unika\Bag::$BACKEND_URI,
	'base_url'			=> \Unika\Bag::$BASE_URL,
    'charset' 			=> 'utf-8',
    'locale' 			=> 'en',
    'tmp_dir'			=> \Unika\Bag::$ENGINE_PATH.DIRECTORY_SEPARATOR.'tmp',
    'log_dir'           => \Unika\Bag::$ENGINE_PATH.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'logs',
    
    //valid Native/Database
    'session_default'			=> 'Database', 

    //valid Apc,Memcached,File,Array,Redis
    'cache_default'     => 'Apc'
);