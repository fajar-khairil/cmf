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
    protected $_packages = array();

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
    	parent::__construct($values);
    	$this->init();
    }

    protected function init()
    {
        if( $this['debug'] === True )
        {
            \Symfony\Component\Debug\Debug::enable('E_ALL');
        }

        $this->registerPackage('Unika');

    	$this['helper.array'] = $this->share(function(){
    		return new Helper\Arr(); 
    	});

    	$this['config_loader'] = $this->share(function($app){
    		return new ConfigLoader(ENGINE_PATH.DIRECTORY_SEPARATOR.'config',$app);
    	});

		$this->register(new \Silex\Provider\HttpCacheServiceProvider(),array(
            'http_cache.cache_dir'  => $this['tmp_dir'].DIRECTORY_SEPARATOR.'cache'
        ));
        
		$this->register(new \Silex\Provider\MonologServiceProvider(),array(
			'monolog.logfile'	=> $this['config_loader']->get('log.'.$this['logger_type'])
		));
		
		$this->register(new \Silex\Provider\SecurityServiceProvider());


        $this->register(new \Silex\Provider\RememberMeServiceProvider());

		$this->register(new \Silex\Provider\SessionServiceProvider());

        if( $this['session_type'] == 'Database' )
        {
            $this['session.storage.handler'] = $this->share(function($app){

                $session_pdo = new \PDO(
                    $this['config_loader']->get('session.Database.dsn'),
                    $this['config_loader']->get('session.Database.user'),
                    $this['config_loader']->get('session.Database.password')
                );            
                $session_pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                $session_dboptions = array(
                    'db_table'      => $this['config_loader']->get('session.Database.table'),
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
            $this['session.storage.save_path'] = $this['config_loader']->get('session.Native.path');
        }

		$this->register(new \Silex\Provider\TranslationServiceProvider);

		$this->register(new \Silex\Provider\UrlGeneratorServiceProvider);      	
        
        $this->register(new \Unika\Provider\CacheServiceProvider);

        $this->register(new \Silex\Provider\SwiftmailerServiceProvider);
        $this['swiftmailer.options'] = $this['config_loader']->get('email');

        $this->register(new \Silex\Provider\TwigServiceProvider);

        $this['twig.string'] = $this->share(function ($app) {
            $loader = new \Twig_Loader_String();
            return new \Twig_Environment($loader, $app['twig.options']);          
        });

        $this['theme_backend_path'] = ENGINE_PATH.DIRECTORY_SEPARATOR.'theme'.DIRECTORY_SEPARATOR.'backend';
        $this['theme_frontend_path'] = ENGINE_PATH.DIRECTORY_SEPARATOR.'theme'.DIRECTORY_SEPARATOR.'frontend';
        $this['module_path'] = ENGINE_PATH.DIRECTORY_SEPARATOR.'module';
        $this['default_backend_theme'] =  $this['theme_backend_path'].DIRECTORY_SEPARATOR.'default';

        $this['twig.path'] = [$this['theme_backend_path'],$this['theme_frontend_path']];

        $this['twig.options'] = $this['config_loader']->get('twig');

        $this->register(new \Silex\Provider\ServiceControllerServiceProvider);
        
        $this->register(new \Silex\Provider\WebProfilerServiceProvider);

        $this['profiler.cache_dir'] = $this['tmp_dir'].DIRECTORY_SEPARATOR.'profiler';
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