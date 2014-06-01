<?php
return array(
	'default'	=>	array(
		'name'		=> 	'unika_',
		'domain'	=> 	null,
		'expired'	=> 	\utilphp\util::SECONDS_IN_A_DAY,
		'path'		=>	'/',
		'secure'	=>	False,
		'httpOnly'	=>  True 
	),
	'remember'	=>	array(
		'name'		=> 	'_remember',
		'domain'	=> 	'unikacmf.dev',
		//in second
		'expired'	=> 	\utilphp\util::SECONDS_IN_A_DAY,
		'path'		=>	'/',
		'secure'	=>	False,
		'httpOnly'	=>  False 		
	)
);