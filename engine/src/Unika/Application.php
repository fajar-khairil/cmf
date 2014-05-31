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
    public static $BACKEND_URI = 'administrator';
    public static $ENGINE_PATH = '/';

    protected $_packages = array();
    protected $_environtment = 'production';

    use \Silex\Application\SecurityTrait;
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
    	$this->init($values);
    }

    protected function init($values)
    {
        $this->_detectEnvirontment($values['environtments']);

        //Illuminate\Filesystem
        $this['Illuminate.files'] = $this->share(function(){
            return new \Illuminate\Filesystem\Filesystem();
        });   

        $this['config'] = $this->share(function($app){
            return new \Illuminate\Config\Repository( 
                new \Illuminate\Config\FileLoader( 
                    $app['Illuminate.files'],
                    self::$ENGINE_PATH.DIRECTORY_SEPARATOR.'config' 
                ), 
                $this->_environtment
            );
        }); 

        foreach( $values as $key=>$value )
        {
            $this['config'][$key] = $value;
        }

        $this['debug'] = $this['config']->get('debug',True);

        if( $this['debug'] === True )
        {
            \Symfony\Component\Debug\Debug::enable('E_ALL');
        }     

        $this->registerPackage('Unika');

        $this->register(new \Unika\Provider\IlluminateServiceProvider());

		$this->register(new \Silex\Provider\HttpCacheServiceProvider(),array(
            'http_cache.cache_dir'  => $this['config']['tmp_dir'].DIRECTORY_SEPARATOR.'cache'
        ));
        
		$this->register(new \Silex\Provider\MonologServiceProvider(),array(
			'monolog.logfile'	=> $this['config']->get('log.'.$this['config']['logger_type'])
		));
		
        $security_configs = $this['config']['security'];
        
        foreach( $security_configs as $key=>$value )
        {
            $this['security.'.$key] = $value;
        }

        unset($security_configs);

		$this->register(new \Silex\Provider\SecurityServiceProvider());

        $this->register(new \Silex\Provider\RememberMeServiceProvider());

		$this->register(new \Silex\Provider\SessionServiceProvider());

        if( $this['config']['session_type'] == 'Database' )
        {
            $this['session.storage.handler'] = $this->share(function($app){

                $session_pdo = new \PDO(
                    $this['config']->get('session.Database.dsn'),
                    $this['config']->get('session.Database.user'),
                    $this['config']->get('session.Database.password')
                );            
                $session_pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                $session_dboptions = array(
                    'db_table'      => $this['config']->get('session.Database.table'),
                    'db_id_col'     => 'session_id',
                    'db_data_col'   => 'session_value',
                    'db_time_col'   => 'session_time'               
                );
                return new \Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler(
                    $session_pdo,
                    $session_dboptions
                );
            });
        }
        else
        {
            $this['session.storage.save_path'] = $this['config']->get('session.Native.path');
        }

		$this->register(new \Silex\Provider\TranslationServiceProvider);

		$this->register(new \Silex\Provider\UrlGeneratorServiceProvider);      	

        $this->register(new \Silex\Provider\SwiftmailerServiceProvider);
        $this['swiftmailer.options'] = $this['config']->get('email');

        $this->register(new \Silex\Provider\TwigServiceProvider);

        $this['twig.string'] = $this->share(function ($app) {
            $loader = new \Twig_Loader_String();
            return new \Twig_Environment($loader, $app['twig.options']);          
        });

        $this['config']['theme_backend_path'] = self::$ENGINE_PATH.DIRECTORY_SEPARATOR.'theme'.DIRECTORY_SEPARATOR.'backend';
        $this['config']['theme_frontend_path'] = self::$ENGINE_PATH.DIRECTORY_SEPARATOR.'theme'.DIRECTORY_SEPARATOR.'frontend';
        $this['config']['module_path'] = self::$ENGINE_PATH.DIRECTORY_SEPARATOR.'module';

        $this['twig.path'] = [$this['config']['theme_backend_path'],$this['config']['theme_frontend_path']];

        $this['twig.options'] = $this['config']->get('twig');

        $this->register(new \Silex\Provider\ServiceControllerServiceProvider);
        
        $this->register(new \Silex\Provider\WebProfilerServiceProvider);

        $this['profiler.cache_dir'] = $this['config']['tmp_dir'].DIRECTORY_SEPARATOR.'profiler';
    }

    protected function _detectEnvirontment(array $environtments)
    {
        foreach( $environtments as $env=>$machine )
        {
            if( in_array(gethostname(), $machine) )
            {
                $this->_environtment = $env;
                break;
            }
        }
    }

    public function detectEnvirontment()
    {
        return $this->_environtment;
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