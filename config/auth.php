<?php
return array(
	//specify default auth driver (Eloquent,Native,Memcache)
	'driver'				=>  'Eloquent',	
	'session_key'			=>  'UNIKA_AUTH',
	'max_failed_attempt'	=> 5,
	//prevent brute force attack and other authentication issues
	'guard_enabled'			=> True,
	'restrict_ip'			=> True,
	/**
	 *	
	 *	you must fill session_info_table if you enabled restrict_ip or enabled_session_info
	 */
	'session_info_table'	=> 'session_info',
	/**
	 *	which cookie config to use for remember_me
	 *	please refer to config/cookie.*
	 */
	'cookie_remember'		=>	'remember',
	'remember_token_column'	=>	'remember_token',

	//implementation
	'Eloquent'				=>	array(
		'user_table'		=>	'users',
		'user_class'		=>  'Model_User'
	)
);