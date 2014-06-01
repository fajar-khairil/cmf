<?php
return array(
    'debug'             => True,
    'backend_uri'       => \Unika\Bag::$BACKEND_URI,
    'base_url'          => \Unika\Bag::$BASE_URL,
    'charset'           => 'utf-8',
    'locale'            => 'en',
    'tmp_dir'           => \Unika\Bag::$ENGINE_PATH.DIRECTORY_SEPARATOR.'tmp',
    'log_dir'           => \Unika\Bag::$ENGINE_PATH.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'logs',
    
    //valid File/Database
    'session_default'          => 'File', 

    //valid Apc,Memcached,File,Array,Redis
    'cache_default'     => 'Apc'
);