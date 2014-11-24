<?php
return array(
	'default'		=> 'eloquent',
	'eloquent'	=> array(
		'resource_table'			=>  'acos',
		'resource_implementation'	=>  'Model_Aco',
		'role_table'				=>	'roles',
		'role_class'				=> 	'Model_Role',
		'acl_table'					=>  'acl'
	)
);