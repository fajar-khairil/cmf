<?php
/**
 *
 *	Bring Spirit of kohana to modern framework composing all goodness from kohanaframework
 *
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Provider;

use Silex\ServiceProviderInterface;

class KohanaServiceProvider implements ServiceProviderInterface
{
	public function register(\Silex\Application $app)
	{
		$app['Kohana.arr'] = $app->share(function(){
			return new \Unika\Kohana\Arr();
		});

		$app['Kohana.text'] = $app->share(function(){
			return new \Unika\Kohana\Text();
		});

		$app['Kohana.debug'] = $app->share(function($app){
			return new \Unika\Kohana\Debug($app);
		});
	}

	public function boot(\Silex\Application $app)
	{

	}
}