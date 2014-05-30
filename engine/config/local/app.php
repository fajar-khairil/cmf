<?php
$backend_uri = 'administrator';
return array(
	'debug' 			=> True,
	'base_url'			=> 'http://unikacmf.dev/',
    'charset' 			=> 'UTF-8',
    'locale' 			=> 'en',
    'tmp_dir'			=> '../tmp',
    'logger_type'		=> 'File',//valid File/Database
    'session_type'			=> 'Database', //valid native/database
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