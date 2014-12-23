<?php
/**
 * This file is part of the UnikaCMF project
 *
 * @author Fajar Khairil <fajar.khairil@gmail.com>
 * @license MIT
 */

namespace Unika\Provider;

use Pimple\Container;
use Unika\ServiceProviderInterface;

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

    /**
     *
     *  return description of provider
     */
    public function getDescription()
    {
        return 'Config Service Provider, currently File only';
    }

    /**
     *
     *  return array of service with each description
     */
    public function getServices()
    {
        return array(
            'config'   	=>  'Illuminate\Config\Repository',
        );
    }

    /**
     *
     *  return an array('author' => '','license' => '','url' => '');
     */
    public function getInfo()
    {
        return array(
            'author'    => 'Fajar Khairil',
            'license'   => 'MIT',
            'url'       => 'http://www.unikacreative.com/',
            'version'	=> '0.1'
        );
    }
}