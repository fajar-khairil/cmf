<?php 
/**
 *	This file is part of the UnikaCMF project
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika;

use Silex\Application as SilexApp;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Application extends SilexApp
{
	use \Silex\Application\UrlGeneratorTrait;
	use \Silex\Application\TranslationTrait;

	const VERSION = '0.0.1-DEV';

	//public static constant
	public static $ROOT_DIR = '';
	public static $ENVIRONMENT  = 'production';
	

	//static Application instance
	protected static $instance = null;

	// instance of Composer\Autoload\ClassLoader
	protected $loader;

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

        $this['resolver'] = function ($app){
            return new Ext\ControllerResolver($app, $app['logger']);
        };

		$this->register(new \Unika\Provider\IlluminateServiceProvider());
		$this->register(new \Unika\Provider\ConfigServiceProvider());
		$this->register(new \Unika\Provider\SymfonyServiceProvider());
		
		$this['debug'] = $this->config['app.debug'];
		$this['locale'] = $this->config('app.default_locale');
		$this['baseUrl'] = $this->config('app.base_url','/');
		
		if( $this['debug'] )
		{
			\Symfony\Component\Debug\Debug::enable(E_ALL);
		}

		/** register console if we are on cli mode */
		if( 'cli' === PHP_SAPI ){
			$this['console'] = new \Unika\Console('UnikaCommander','0.1-dev');
			$this['console']->setContainer($this);
		}

		$this['util'] = new \Unika\Util;
		$this['sec.util'] = new \Unika\Security\Util;
		$this['path.base'] = static::$ROOT_DIR;
		$this['path.module'] = $this['path.base'].'/code';
		$this['path.themes'] = $this['path.base'].'/themes';
		$this['path.var']	=	$this['path.base'].'/var';

		static::$instance = $this;
	}

    /**
     * Registers a service provider.
     *
     * @param ServiceProviderInterface $provider A ServiceProviderInterface instance
     * @param array                    $values   An array of values that customizes the provider
     *
     * @return Application
     */
    public function register(\Pimple\ServiceProviderInterface $provider, array $values = array())
    {
    	parent::register($provider,$values);
    	if( 'cli' === PHP_SAPI AND $provider instanceof \Unika\Interfaces\CommandProviderInterface ){
    		$provider->addCommand($this['console']);
    	}

    	return $this;
    }

	public function config($key = null,$default = null)
	{
		if( null === $key )
			return $this['config'];
		
		return $this['config']->get($key,$default);
	}

	public function createResponse($body,$code = 200)
	{
		return new \Symfony\Component\HttpFoundation\Response($body,$code);
	}

	public static function detectEnvironment($detectfunct)
	{
        if (!is_object($detectfunct) || !method_exists($detectfunct, '__invoke')) {
            throw new \InvalidArgumentException('Service definition is not a Closure or invokable object.');
        }

		self::$ENVIRONMENT = $detectfunct();
	}

	public function getProvider($providerName = null)
	{
		if( null === $providerName )
			return $this->providers;

		return $this->providers[$providerName];
	}

	public function setLoader(\Composer\Autoload\ClassLoader $loader)
	{
		$this->loader = $loader;
	}

	public function getLoader()
	{
		return $this->loader;
	}

	public function convertRequestUri($uri)
	{
	    $argv = explode('/', $uri);

    	$ctrlName = ( empty($argv[1]) ) ? 'Index' : ucfirst($argv[1]);
		
		$actionName = ( empty($argv[2]) ) ? 'index' : strtolower($argv[2]);
		return array(
			'controller'	=> $ctrlName,
			'action'		=> $actionName 
		);
	}

	function __get($name)
	{
		return $this[$name];
	}
}