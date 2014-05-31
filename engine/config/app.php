<?php
$backend_uri = 'administrator';
$base_url = 'http://unikacmf.dev/';
return array(
	'debug' 			=> False,
    'backend_uri'       => $backend_uri,
	'base_url'			=> $base_url,
    'charset' 			=> 'UTF-8',
    'locale' 			=> 'en',
    'tmp_dir'			=> ENGINE_PATH.DIRECTORY_SEPARATOR.'tmp',
    //valid File/Database
    'logger_type'		=> 'File',
    //valid Native/Database
    'session_type'			=> 'Database', 
    //valid Apc,Memcached,File,Array,Redis
    'cache.default'     => 'Apc',
    'security.firewalls' => array(
        'default'       => array(
            'anonymous'  => True,
            'pattern'    =>  '^/'.$backend_uri.'/login$'
        ),
    	'backend'		=> array(
    		'pattern'	=> '^/'.$backend_uri.'$',
    		'form' => array(
                'login_path' => $base_url.$backend_uri.'/login',
                'check_path' => $base_url.$backend_uri.'/login_check' ,
                'logout_path' => $base_url.$backend_uri.'/logout'
            ),
    		'remember_me' => array(
    			'key'	=> 'HbD(Y&*yihd78UIjdjks77hndsfsdFGsd4',
    			'always_remember_me'	=> True
    		)
    	)
    ),
    'security.access_rules' => array(
        array('^/'.$backend_uri.'/login$', 'IS_AUTHENTICATED_ANONYMOUSLY'),
        array('^/'.$backend_uri.'$', 'ROLE_USER')
    )
);