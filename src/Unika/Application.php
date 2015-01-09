<?php 
/**
 *	This file is part of the UnikaCMF project
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika;

use Silex\Application as SilexApp;

class Application extends SilexApp
{
	use \Silex\Application\UrlGeneratorTrait;

	const VERSION = '0.0.1-DEV';

	//public static constant
	public static $ROOT_DIR = '';
	public static $ENVIRONMENT  = 'production';
	

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

		$this->config = $this['config'];
		$this['debug'] = $this->config['app.debug'];

		if( $this['debug'] )
		{
			\Symfony\Component\Debug\ErrorHandler::register();
			if( !extension_loaded('xdebug') )
				$this->register(new \Unika\Provider\WhoopsServiceProvider());
		}

		static::$instance = $this;
	}

	public function config($key = null,$default = null)
	{
		if( $key === null )
			return $this->config;
		
		return $this->config->get($key,$default);
	}

	public function createResponse($body,$code = 200)
	{
		return new \Symfony\Component\HttpFoundation\Response($body,$code);
	}

	public function detectEnvironment($detectfunct)
	{
        if (!is_object($detectfunct) || !method_exists($detectfunct, '__invoke')) {
            throw new \InvalidArgumentException('Service definition is not a Closure or invokable object.');
        }

		self::$ENVIRONMENT = $detectfunct();
	}

	function __get($name)
	{
		return $this[$name];
	}
}