<?php
return array(
	// default authentication driver implementation
	'default'	=> 'database',

	// session name to store authentication session
	'session_name'	=> 'app_auth',
	
	/** Class must implement Unika\Security\PasswordHasherInterface */
	'password_hasher_class'	=>	'\Unika\Security\Util',

	/**  guard configuration */
	'guard'	=> array(
		'active'	=>	False,
		'throttling_count'	=> 5
	),
	
	/** remember me */
	'remember_me'	=>	array(
		'cookie_name'		=>	'auth_remember',
		'default_timeout'	=> 30
	),
	
	/** availables drivers */
	'drivers'	=> array(
		'database'	=> array(
			'connection_name'		=> 'master',
			'users_table'			=>	'users',
			'session_info_table'	=> 'session_info'
		)
	)
);