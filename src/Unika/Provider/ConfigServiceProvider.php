<?php
/**
 * This file is part of the Unika-CMF project
 *
 * @author Fajar Khairil
 * @license MIT
 */

namespace Unika\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ConfigServiceProvider implements ServiceProviderInterface
{
	public function register(Container $app)
	{
		$app['config.file.default_path'] = $app::$ROOT_DIR.DIRECTORY_SEPARATOR.'config';
		$app['config'] = function($app){
			return new \Illuminate\Config\Repository(
				new \Illuminate\Config\FileLoader(new \Illuminate\Filesystem\Filesystem,$app['config.file.default_path']),
				$app::$ENVIRONMENT
			);
		};
	}
}