<?php
$base_url = 'http://unikacmf.dev/';
return array(
    'debug'             => True,
    'backend_uri'       => \Unika\Application::$BACKEND_URI,
    'base_url'          => $base_url,
    'charset'           => 'utf-8',
    'locale'            => 'en',
    'tmp_dir'           => \Unika\Application::$ENGINE_PATH.DIRECTORY_SEPARATOR.'tmp',
    //valid File/Database
    'logger_type'       => 'File',
    //valid Native/Database
    'session_type'          => 'Database', 
    //valid Apc,Memcached,File,Array,Redis
    'cache.default'     => 'Apc'
);