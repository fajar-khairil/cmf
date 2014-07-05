<?php
/**
 *  This file is part of the Unika-CMF project.
 *  Unika\Application Extending \Silex\Application
 *
 *  @license : MIT 
 *  @author  : Fajar Khairil
 */

class Application extends \Silex\Application
{    
    protected static $_environtment = 'production';
    protected static $_environtmentDetected = False;

    public static $BACKEND_URI = 'administrator';
    public static $ENGINE_PATH = '/';
    public static $BASE_URL = '/';
    
    protected static $instance = null;

    //SplFixedArray for storing view paths
    protected $view_path_fixed;

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

        $default_backend_theme =  Application::$ENGINE_PATH.DIRECTORY_SEPARATOR.'themes'.
                                DIRECTORY_SEPARATOR.'backend';

        $this->registerViewPath($default_backend_theme);

        $this['view.paths'] = function($app){
            return $app->view_path_fixed->toArray();
        };

        $this->register(new \Unika\Provider\SecurityServiceProvider);
        $this->register(new \Unika\Provider\CacheServiceProvider);
        $this->register(new \Unika\Provider\CapsuleServiceProvider);      
        $this->register(new \Unika\Provider\ViewServiceProvider);
        $this->register(new \Unika\Provider\TwigServiceProvider);
        $this->register(new \Unika\Provider\AclServiceProvider);

        $this->register(new \Silex\Provider\SessionServiceProvider());
        $this->register(new \Silex\Provider\TranslationServiceProvider);       
        $this->register(new \Silex\Provider\SwiftmailerServiceProvider);
        $this->register(new \Silex\Provider\ServiceControllerServiceProvider);        
        $this->register(new \Silex\Provider\RoutingServiceProvider);  
        
        $this->initCommonContainers(); 
        $this['config']['modules_path'] = Application::$ENGINE_PATH.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR;
    
        static::$instance = $this;
    }

    public function registerViewPath($path)
    {
        $this->view_path_fixed->next();
        $key = $this->view_path_fixed->key();
        if( $key === 1 )
            $key = 0;
        else
            $key = $key - 1;

        $this->view_path_fixed[ $key ] = $path;

        //if there is no more room in FixedArray increase the size
        if( !$this->view_path_fixed->valid() )
            $this->view_path_fixed->setSize( $this->view_path_fixed->count() + 64 );
    }

    public function config()
    {
        return $this['config'];
    }

    public function initCommonContainers()
    {
        $this['SessionManager'] = new \Unika\Common\SessionWrapper($this);

        $this->app['session.storage.save_path'] = $this['config']->get('session.File.path');
        if( !in_array($this['config']['session.default'], array('Database','Mongodb','Memcached') ) )
        {
            return True;
        }

        $this['session.storage.handler'] = $this['SessionManager']->getSession($this['config']['session.default']);

        $this['cookie'] = new \Unika\Common\CookieWrapper($this);

        $this['response'] = $this->factory(function($app){
            return new \Symfony\Component\HttpFoundation\Response();
        });

        $this->register(new \Silex\Provider\HttpCacheServiceProvider(),array(
            'http_cache.cache_dir'  => $this['config']['app.tmp_dir'].DIRECTORY_SEPARATOR.'cache'
        ));
        
        $this->register(new \Silex\Provider\MonologServiceProvider(),array(
            'monolog.logfile'   => $this['config']->get('app.log_dir').DIRECTORY_SEPARATOR.'application.log'
        ));

        $this['swiftmailer.options'] = $this['config']->get('email');                           

        $this['PasswordLib'] = new \PasswordLib\PasswordLib();

        $this['signer'] = new Symfony\Component\HttpKernel\UriSigner($this['config']['app.secret_key']);

        $this['request'] = $this->factory(function(){ 
            return $this['request_stack']->getCurrentRequest();    
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
