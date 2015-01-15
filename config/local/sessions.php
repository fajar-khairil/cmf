<?php
return array(
	'default'	=>	'File',
	'File'	=>	array(
		'name'		=>	'UNIKA_SESS',
		'lifetime'	=>	1800,// 3oMin
		'domain'	=> $_SERVER['HTTP_HOST'],
		'secure'	=> False,
		'httponly'	=> True
	)
);