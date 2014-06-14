<?php
return array(
	'twig'	=> array(
		'cache'		=>	Application::$ENGINE_PATH.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'twig',
	),
	'blade'	=> array(
		'cache'		=>	Application::$ENGINE_PATH.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'blade',
		'content_tags'	=>	['[[',']]'],
		'escaped_content_tags'	=>	['[[[',']]]']
	)

);