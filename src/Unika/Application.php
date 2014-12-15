<?php 
/**
 *	This file is part of the Unika-CMF project
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika;

use Silex\Application as SilexApp;

class Application extends SilexApp
{
	const VERSION = '0.0.1-DEV';

	//public static constant
	public static $ROOT_DIR = '';
	public static $ENVIRONMENT = 'production';//production by default

	//static Application instance
	protected static $instance = null;

	protected $config;

	public static function instance()
	{
        if( static::$instance === NULL ){
           static::$instance = new static();
        }
        return static::$instance;
	}

	public function  __construct(array $values = array())
	{
		parent::__construct($values);

		$this->register(
			new \Unika\Provider\ConfigServiceProvider()
		);

		$this['illuminate.filesystem'] = new \Illuminate\Filesystem\Filesystem();

		$this->config = $this['config'];
		$this['debug'] = $this->config['app.debug'];

		if( $this['debug'] )
		{
			\Symfony\Component\Debug\ErrorHandler::register();
			$this->register(new \Unika\Provider\WhoopsServiceProvider());
		}

		$this->register(new \Unika\Provider\MonologServiceProvider(),
			array(
				'monolog.logfile'		=>	$this::$ROOT_DIR.'/var/logs/application.log',
				'monolog.permission'	=>  0777
			)
		);

		$this->register(
			new \Unika\Provider\ViewServiceProvider()
		);

		static::$instance = $this;
	}

	public function config($key = null)
	{
		if( $key === null )
			return $this->config;
		
		return $this->config[$key];
	}

	public function createResponse($body,$code = 200)
	{
		return new \Symfony\Component\HttpFoundation\Response($body,$code);
	}
}