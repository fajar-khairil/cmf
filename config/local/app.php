<?php
return array(
	// name of application
	'name'			=> 'UnikaCMF',

	// are we on debug mode ?
	'debug'			=> 	True,

	// multilanguage route support ?
	'multilanguage'	=>  True,

	// minimal level to log Monolog\Logger CONSTANT
	'debug_level'	=> 400,
	
	// default theme to use
	'default_theme'	=> 'default',

	'base_url'		=> '//unikacmf.dev/',

	// supported localizations
	'locales'		=> array('id','en','fr'),
	
	//default localization or fallback
	'default_locale'=> 'id',
);