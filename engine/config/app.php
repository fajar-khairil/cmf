<?php
$backend_uri = 'administrator';
return array(
	'debug' 			=> True,
	'base_url'			=> 'http://unikacmf.dev/',
    'charset' 			=> 'UTF-8',
    'locale' 			=> 'en',
    'tmp_dir'			=> '../tmp',
    //valid File/Database
    'logger_type'		=> 'File',
    //valid Native/Database
    'session_type'			=> 'Database', 
    //valid Apc,Memcached,File,Array,Redis
    'cache.default'     => 'Apc',
    'security.firewalls' => array(
    	'default'		=> array(
    		'pattern'	=> '^/'.$backend_uri.'$',
    		'form'		=> True,
    		'logout'	=> True,
    		'remember_me' => array(
    			'key'	=> 'HbD(Y&*yihd78UIjdjks77hndsfsdFGsd4',
    			'always_remember_me'	=> True
    		)
    	)
    )
);