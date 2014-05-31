<?php
$backend_uri = 'administrator';
return array(
	'debug' 			=> True,
    'backend_uri'       => $backend_uri,
	'base_url'			=> 'http://unikacmf.dev/',
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
                'login_path' => '/'.$backend_uri.'/login',
                'check_path' => '/'.$backend_uri.'/login_check' ,
                'logout_path' => '/'.$backend_uri.'/logout'
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