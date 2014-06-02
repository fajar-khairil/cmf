<?php
/**
 *	This file is part of the Unika-CMF project.
 *	SessionWrapper
 *	
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Common;

class SessionWrapper
{

	protected $app;
	protected $resolved = array();

	function __construct( )
	{
		$this->app = \Application::instance();
	}

	public function getSession( $session_name )
	{	
		if( !in_array($session_name, array('Database','Mongodb','Memcached') ) )
			$session_name = 'File';

		if( !isset( $this->resolved[$session_name] ) )
		{
			$this->resolved[$session_name] = $this->_getSession($session_name);
		}
		
		return $this->resolved[$session_name];
	}

	protected function _getSession( $session_name )
	{
        switch($session_name)
        {
	        case 'Database' :
	            $session_pdo = new \PDO(
	                $this->app['config']->get('session.Database.dsn'),
	                $this->app['config']->get('session.Database.user'),
	                $this->app['config']->get('session.Database.password')
	            );            

	            $session_pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
	            $session_dboptions = array(
	                'db_table'      => $this->app['config']->get('session.Database.table'),
	                'db_id_col'     => 'session_id',
	                'db_data_col'   => 'session_value',
	                'db_time_col'   => 'session_time'               
	            );
	            
	            if( $this->app['config']['auth.restrict_ip'] === True )
	            {	//use our own PdoSessionHandler
		            return new PdoSessionHandler(
		                $session_pdo,
		                $session_dboptions
		            );
		        }
		        else
		        {
		            return new \Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler(
		                $session_pdo,
		                $session_dboptions
		            );		        	
		        }
	            
	            break;
	        case 'Mongodb' : 
	                $mongo = null;
	                $mongo = ( class_exists('\\MongoClient') )? new \MongoClient() : null;
	                if( $mongo === null ){
	                    throw new \RuntimeException('Cannot use MongoDB as SessionHandler extensions not exists.');
	                }

	                return new \Symfony\Component\HttpFoundation\Session\Storage\Handler\MongoDbSessionHandler();
	            break;
	        case 'Memcached' : 
	            if( class_exists('\\Memcached') )
	            {
	                return new \Symfony\Component\HttpFoundation\Session\Storage\Handler\MemcachedSessionHandler(new \Memcached,$this->app['config']['session.Memcached']);
	            }
	            elseif( class_exists('\\Memcache') )
	            {
	                return new \Symfony\Component\HttpFoundation\Session\Storage\Handler\MemcacheSessionHandler(new \Memcache,$this->app['config']['session.Memcached']);
	            }

	            return new \Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler($this->app['session.storage.save_path']);
	            break;
	        default :
	            return new \Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler($this->app['session.storage.save_path']);
	            break;
        }		
	}
}