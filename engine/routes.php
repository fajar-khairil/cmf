<?php
$app->get('/',function() use($app){
	return new \Symfony\Component\HttpFoundation\Response(
		$app['twig']->render('default/layout.twig',array('name' => "Fajar Khairil"))
	);
});