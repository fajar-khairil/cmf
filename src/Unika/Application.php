<?php 
/**
 *	This file is part of the Unika-CMF project
 *	
 *	@license MIT
 *	@author Fajar Khairil
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

	public $config;

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
			$this->register(new \Unika\Provider\WhoopsServiceProvider());
		}

		$this->register(
			new \Unika\Provider\MonologServiceProvider(),
			array(
				'monolog.logfile'	=>	$this::$ROOT_DIR.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.'application.log'
			)
		);

		$this->register(
			new \Unika\Provider\ViewServiceProvider()
		);

		static::$instance = $this;
	}

	public function createResponse($body,$code = 200)
	{
		return new \Symfony\Component\HttpFoundation\Response($body,$code);
	}
}