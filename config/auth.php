<?php
return array(
	/**
	 *
	 *	authentication default implementation
	 *	current valid value : Eloquent ,  in the future we would support simple,pdo
	 */
	'default'			=>	'Eloquent',
	'session_key'		=>  'UNIKA_AUTH',

	/**
	 *
	 *	which cookie config to use for remember_me
	 *	please refer to config/cookie.*
	 */
	'cookie_remember'	=>	'remember',
	'remember_token_column'	=>	'remember_token',
	'Eloquent'			=>	array(
		'user_table'	=>	'users'
	)
);