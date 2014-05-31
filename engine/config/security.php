<?php
/*$security_configs = array(
    'firewalls' => array(
        'default'       => array(
            'anonymous'  => True,
            'pattern'    =>  '^/login$'
        ),
    	'backend'		=> array(
    		'pattern'	=> '^/'.\Unika\Application::$BACKEND_URI.'*.$',
    		'form' => array(
                'login_path' => \Unika\Application::$BASE_URL.\Unika\Application::$BACKEND_URI.'/login',
                'check_path' => \Unika\Application::$BASE_URL.\Unika\Application::$BACKEND_URI.'/login_check' ,
                'logout_path' => \Unika\Application::$BASE_URL.Unika\Application::$BACKEND_URI.'/logout'
            ),
    		'remember_me' => array(
    			'key'	=> 'HbD(Y&*yihd78UIjdjks77hndsfsdFGsd4',
    			'always_remember_me'	=> True
    		)
    	)
    ),
    'access_rules' => array(
        array(\Unika\Application::$BASE_URL.\Unika\Application::$BACKEND_URI.'/login$', 'IS_AUTHENTICATED_ANONYMOUSLY'),
        array(\Unika\Application::$BASE_URL.\Unika\Application::$BACKEND_URI.'$', 'ROLE_USER')
    )
);

return $security_configs;*/
return array('firewalls' =>  'dummy');