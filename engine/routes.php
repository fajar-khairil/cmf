<?php

$app->get('/',function(){
	return new \Symfony\Component\HttpFoundation\Response('hello world!');
});