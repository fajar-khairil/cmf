<?php
return array(
	'driver'	=> 'database',
	/** Class must implement Unika\Security\PasswordHasherInterface */
	'password_hasher_class'	=>	'\Unika\Security\Util',
	/** availables drivers */
	'database'	=> array(
		'users_table'	=>	'users'
	),
);