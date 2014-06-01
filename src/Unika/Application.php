<?php
/**
 *
 *  Unika\Application Extending \Silex\Application
 *
 *  @license : MIT 
 *  @author  : Fajar Khairil
 */
namespace Unika;

class Application extends \Silex\Application
{    
    protected static $_environtment = 'production';
    protected static $_environtmentDetected = False;

 
    protected $_packages = array();

    use \Silex\Application\FormTrait;
    use \Silex\Application\UrlGeneratorTrait;
    use \Silex\Application\SwiftmailerTrait;
    use \Silex\Application\MonologTrait;
    use \Silex\Application\TranslationTrait;
    use \Silex\Application\TwigTrait;	

    /**
     * Instantiate a new Application.
     *
     * Objects and parameters can be passed as argument to the constructor.
     *
     * @param array $values The parameters or objects.
     */
    public function __construct(array $values = array())
    {
    	parent::__construct();
        $this->init();
    }

    protected function init()
    {        
        //Illuminate\Filesystem
        $this['Illuminate.files'] = $this->share(function(){
            return new \Illuminate\Filesystem\Filesystem();
        });   

        $this['config'] = $this->share(function($app){
            return new \Illuminate\Config\Repository( 
                new \Illuminate\Config\FileLoader( 
                    $app['Illuminate.files'],
                    Bag::$ENGINE_PATH.DIRECTORY_SEPARATOR.'config' 
                ), 
                static::detectEnvirontment()
            );
        });

        $this['config']['engine_path'] = Bag::$ENGINE_PATH;

        $this['debug'] = $this['config']->get('app.debug',True);

        if( $this['debug'] === True )
        {
            \Symfony\Component\Debug\Debug::enable('E_ALL');
        }     

        $this->registerPackage('Unika');

        $this->initSessions();

        $this->initTwig();

        $this->initCommonProviders();              
    }

    public function initCommonProviders()
    {
        $this->register(new \Unika\Provider\IlluminateServiceProvider());

        $this->register(new \Silex\Provider\HttpCacheServiceProvider(),array(
            'http_cache.cache_dir'  => $this['config']['app.tmp_dir'].DIRECTORY_SEPARATOR.'cache'
        ));
        
        $this->register(new \Silex\Provider\MonologServiceProvider(),array(
            'monolog.logfile'   => $this['config']->get('app.log_dir').DIRECTORY_SEPARATOR.'access.log'
        ));

        $this->register(new \Silex\Provider\TranslationServiceProvider);

        $this->register(new \Silex\Provider\UrlGeneratorServiceProvider);       

        $this->register(new \Silex\Provider\SwiftmailerServiceProvider);
        $this['swiftmailer.options'] = $this['config']->get('email');      

        $this->register(new \Silex\Provider\ServiceControllerServiceProvider);
        
        $this->register(new \Silex\Provider\WebProfilerServiceProvider);

        $this['profiler.cache_dir'] = $this['config']['app.tmp_dir'].DIRECTORY_SEPARATOR.'profiler';

        $this['PasswordLib'] = $this->share(function(){
            return new \PasswordLib\PasswordLib();
        });
    }

    protected function initTwig()
    {
        $this->register(new \Silex\Provider\TwigServiceProvider);

        $this['twig.string'] = $this->share(function ($app) {
            $loader = new \Twig_Loader_String();
            return new \Twig_Environment($loader, $app['twig.options']);          
        });

        $this['config']['theme_backend_path'] = Bag::$ENGINE_PATH.DIRECTORY_SEPARATOR.'theme'.DIRECTORY_SEPARATOR.'backend';
        $this['config']['theme_frontend_path'] = Bag::$ENGINE_PATH.DIRECTORY_SEPARATOR.'theme'.DIRECTORY_SEPARATOR.'frontend';
        $this['config']['module_path'] = Bag::$ENGINE_PATH.DIRECTORY_SEPARATOR.'module';

        $this['twig.path'] = [ $this['config']['theme_backend_path'],$this['config']['theme_frontend_path'] ];

        $this['twig.options'] = array(
            'charset'          => $this['config']['app.charset'],
            'debug'            => $this['config']['app.debug']
        );
    }

    protected function initSessions()
    {
        $this->register(new \Silex\Provider\SessionServiceProvider());

        $this['SessionManager'] = $this->share(function(){
            return new \Unika\Common\SessionWrapper();
        });

        $this->app['session.storage.save_path'] = $this['config']->get('session.File.path');
        if( !in_array($this['config']['session.default'], array('Database','Mongodb','Memcached') ) )
        {
            return True;
        }

        $this['session.storage.handler'] = $this->share(function($app)
        {
            return $app['SessionManager']->getSession($this['config']['session.default']);
        });

        $this['cookie'] = function($app){
            return new \Unika\Common\CookieWrapper($app);
        };
    }

    public static function detectEnvirontment(array $environtments = null)
    {
        if( $environtments === null )
        {
            return static::$_environtment;
        }

        if( static::$_environtmentDetected ) return static::$_environtment;

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

    /**
     *
     *  Registering Module/Theme namespace
     */
    public function registerPackage($namespace)
    {
        $this->_packages[] = $namespace;
    }
}