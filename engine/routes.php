<?php

$app->get('/',function(){	
	//return 'Hello World';
	return new \Symfony\Component\HttpFoundation\Response('hello world!');
});