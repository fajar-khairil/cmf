<?php
/**
 *  This file is part of the Unika-CMF project.
 *  Unika\Application Extending \Silex\Application
 *
 *  @license : MIT 
 *  @author  : Fajar Khairil
 */

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class Application extends \Silex\Application
{    
    protected static $_environtment = 'production';
    protected static $_environtmentDetected = False;

    public static $BACKEND_URI = 'administrator';
    public static $ENGINE_PATH = '/';
    
    protected static $instance = null;

    use \Silex\Application\UrlGeneratorTrait;
    use \Silex\Application\SwiftmailerTrait;
    use \Silex\Application\TranslationTrait;

    public static function instance()
    {
        if( static::$instance === NULL ){
            throw new \RuntimeException('Cannot get instance when Application not yet constructed.');
        }

        return static::$instance;
    }

    /**
     * Instantiate a new Application.
     *
     * Objects and parameters can be passed as argument to the constructor.
     *
     * @param array $values The parameters or objects.
     */
    public function __construct(array $values = array())
    {     
    	parent::__construct($values);
        $this->view_path_fixed = new \SplFixedArray(64);//internal

        //config depend on Illuminate/config
        $this->register(new \Unika\Provider\IlluminateServiceProvider());

        $this['config']['engine_path'] = Application::$ENGINE_PATH;

        $this['debug'] = $this['config']->get('app.debug',True);

        if( static::$_environtment === 'production' )
            $this['debug'] = False;        

        if( $this['debug'] === True )
        {
            \Symfony\Component\Debug\Debug::enable('E_ALL');
        }              
       
        $this->initCommonServices();        
        
        $this['config']['modules_path'] = Application::$ENGINE_PATH.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR;
    
        static::$instance = $this;
    }

    public function config()
    {
        return $this['config'];
    }

    public function initCommonServices()
    {
        $this['swiftmailer.options'] = $this['config']->get('email');                           

        $this['PasswordLib'] = new \PasswordLib\PasswordLib();

        $this['signer'] = new Symfony\Component\HttpKernel\UriSigner($this['config']['app.secret_key']); 
        
        $this['cookie'] = new \Unika\Common\CookieWrapper($this);

        $this['request'] = $this->factory(function(){ 
            return $this['request_stack']->getCurrentRequest();    
        });  

        $this['response'] = $this->factory(function($app){
            return new \Symfony\Component\HttpFoundation\Response();
        });    
    }

    public static function detectEnvirontment(array $environtments = null)
    {
        //prevent user to detect environtment twice
        if( $environtments === null OR static::$_environtmentDetected === True )
        {
            return static::$_environtment;
        }

        foreach( $environtments as $env=>$machine )
        {
            if( in_array(gethostname(), $machine) )
            {
                static::$_environtment = $env;
                static::$_environtmentDetected = True;
                break;
            }
        }
        return static::$_environtment;
    }  

    public static function isWindows()
    {
        return (DIRECTORY_SEPARATOR === '\\');
    }

    public static function isUnix()
    {
        return (DIRECTORY_SEPARATOR === '/');
    }
}
