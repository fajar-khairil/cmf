<?php
return array(
    'debug'             => True,
    'backend_uri'       => Application::$BACKEND_URI,
    'base_url'          => Application::$BASE_URL,
    'charset'           => 'utf-8',
    'locale'            => 'en',
    'tmp_dir'           => Application::$ENGINE_PATH.DIRECTORY_SEPARATOR.'tmp',
    'log_dir'           => Application::$ENGINE_PATH.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'logs',
    
    //valid File/Database
    'session_default'          => 'File', 

    //valid Apc,Memcached,File,Array,Redis
    'cache_default'     => 'Apc'
);