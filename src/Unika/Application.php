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
	use \Silex\Application\TranslationTrait;

	const VERSION = '0.0.1-DEV';

	//public static constant
	public static $ROOT_DIR = '';
	public static $ENVIRONMENT  = 'production';
	

	//static Application instance
	protected static $instance = null;

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

		$this['debug'] = $this->config['app.debug'];

		if( $this['debug'] )
		{
			\Symfony\Component\Debug\ErrorHandler::register();
		}

		/** register console if we are on cli mode */
		if( 'cli' === PHP_SAPI ){
			$this['console'] = new \Unika\Console('UnikaCommander','0.1-dev');
			$this['console']->setContainer($this);
		}

		$this['util'] = new \Unika\Util;
		$this['sec.util'] = new \Unika\Security\Util;

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
    		$provider->command($this['console']);
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

	public function detectEnvironment($detectfunct)
	{
        if (!is_object($detectfunct) || !method_exists($detectfunct, '__invoke')) {
            throw new \InvalidArgumentException('Service definition is not a Closure or invokable object.');
        }

		self::$ENVIRONMENT = $detectfunct();
	}

	public function getProvider($providerName)
	{
		return $this->providers[$providerName];
	}

	public function getProviders()
	{
		return $this->providers;
	}

	function __get($name)
	{
		return $this[$name];
	}
}