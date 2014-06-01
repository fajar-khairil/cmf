<?php
return array(
	/**
	 *
	 *	authentication default implementation
	 *	current valid value : Eloquent ,  in the future we would support simple,pdo
	 */
	'default'			=>	'Eloquent',
	'session_key'		=>  'UNIKA_AUTH',

	//implementation
	'Eloquent'				=>	array(
		'user_table'		=>	'users'
	),

	//Remember me configurations
	'enabled_remember_me'	=> True,
	//allow remember me on this computer
	'restrict_ip'			=> True,
	'enabled_session_info'	=> True,
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
	'remember_token_column'	=>	'remember_token'
);