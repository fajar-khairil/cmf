<?php
return array(
	'default'		=> 'eloquent',
	'eloquent'	=> array(
		'resource_table'		=>  'acos',
		'role_table'	=>		'roles',
		'role_class'	=>  	'\Unika\Security\Authorization\Eloquent\Role',
		'resource_class'		=>  ''
	)
);