<?php
/**
 *	This file is part of the UnikaCMF project
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika\Security\Authentication\Driver;

use Unika\Security\Authentication\AuthDriverInterface;
use Unika\Security\Authentication\AuthException;

class AuthDatabase implements AuthDriverInterface
{
	protected $app;
	protected $db;
	protected $connectionName;
	protected $throttle;
	protected $user = null; //cached user

	public function __construct(\Unika\Application $app,$connectionName = null,$throttle_guard = True)
	{
		$this->app = $app;
		if( null === $connectionName )
			$connectionName = $this->app->config('database.default');

		$this->connectionName = $connectionName;
		$this->db = $this->getQuery();
		$this->throttle = (boolean)$throttle_guard;
		$this->init();
	}


	//return query builder
	protected function getQuery()
	{
		return $this->app['database']->table( $this->app->config('auth.drivers.database.users_table') ,$this->connectionName );
	}

	protected function init()
	{
		$self = $this;
		$this->app['Illuminate.events']->listen('auth.success',function($credentials,$col,$remember,$timeout,$auth) use($self){
			$self->doOnSucess($credentials,$col,$remember,$timeout,$auth);
		});

		$this->app['Illuminate.events']->listen('auth.failure',function($credentials,$col) use($self){
			$self->doOnFailure($credentials,$col);
		});			
	}

	protected function doOnFailure($credentials,$col)
	{
		$_sql = 'UPDATE '.$this->app->config('auth.drivers.database.users_table').' 
		SET last_failed_count = last_failed_count + 1 WHERE '.$col.' = ?';

		$bindings = array(
			$credentials[$col]
		);

		$this->app['database']->getConnection($this->connectionName)->update($_sql,$bindings);
	}

	/**
	 *
	 *	@return user or null if not found
	 */
	public function resolveUser($field,$value)
	{
		if( null === $this->user )
		{		
			$this->user = $this->db
				->select('users.*','roles.name as role_name')
				->join('roles','users.role_id','=','roles.id')
				->where($field,'=',$value)
				->first();
		}

		return $this->user;
	}

	/**
	 *
	 *	@return True on blocked , null if credential not found
	 */
	public function isBlocked(array $credentials,$col)
	{
		$user = $this->resolveUser($credentials,$col);
		
		if( $user )
		{
			if( (int)$user['last_failed_count'] >= (int)$this->app->config('auth.guard.throttling_count') )
			{
				$this->getQuery()->from('users')->where('id' ,'=',$user['id'])->update(['active' => 0,'updated_at' =>  date('Y-m-d H:i:s')]);
				return True;
			}
			else
			{
				return False;
			}
		}

		return $user;
	}

	/**
	 *
	 *	Register the user
	 */
	public function register(array $user)
	{
		return $this->app['database']->table('users')->insert($user);
	}

	/**
	 *	check the valiity of remember_me cookie
	 *
	 *	@param integer|string $userId
	 *	@param string $token
	 *	
	 *	@return boolean
	 */
	public function checkRememberMeToken($userId,$token)
	{
		$info = $this->app['database']
				->table($this->app->config('auth.drivers.database.session_info_table'),$this->connectionName)
				->where('user_id',$userId)
				->where('token',$token)
				->first();
	
		if( null === $info ) return False;

		// compare expired token with current datetime, if expired delete the token
		if( True === ( strtotime(date('Y-m-d H:i:s')) >= strtotime($info['expired']) ) )
		{
			$this->app['database']->table($this->app->config('auth.drivers.database.session_info_table'),$this->connectionName)
				->where('user_id',$userId)
				->delete();

			return False;
		}

		return True;
	}

	/**
	 *	set remember_me token
	 *
	 *	@param integer|string $userId
	 *	@param string $token
	 *	@param Date | string $timeout
	 *	@return void
	 */
	public function setRememberMeToken($userId,$token,$timeout)
	{
		$request = $this->app['request_stack']->getCurrentRequest();

		$this->app['database']->table($this->app->config('auth.drivers.database.session_info_table'),$this->connectionName)->insert([
			'token'			=> $token,
			'user_id'		=> $userId,
			'user_agent'	=> $request->server->get('HTTP_USER_AGENT'),
			'ip_address'	=> $request->server->get('REMOTE_ADDR'),
			'expired'		=> $timeout
		]);		
	}

	protected function doOnSucess($credentials,$col,$remember,$timeout,$auth)
	{
		$now = date('Y-m-d H:i:s');
		$this->getQuery()->from('users')->where($col ,'=',$credentials[$col])->update(['last_login' => $now,'updated_at' =>  $now]);
	}

	/**
	 *	@param array $credentials
	 *	@return User
	 */
	public function authenticate($field,$value)
	{
		return $this->resolveUser($credentials);
	}

	public static function createAllTables(\Unika\Application $app ,\Illuminate\Database\Schema\Builder $schema)
	{
		static::createUsersTable($app,$schema);
		static::createSessionInfo($app,$schema);
	}

	public static function createSessionInfo(\Unika\Application $app ,\Illuminate\Database\Schema\Builder $schema)
	{
		return $schema->create($app->config('auth.drivers.database.session_info_table'),function($blueprint)
		{
		  $blueprint->integer('id',True,True);
		  $blueprint->string('token');
		  $blueprint->string('user_agent')->nullable();
		  $blueprint->string('ip_address',128);
		  $blueprint->integer('user_id');
		  $blueprint->dateTime('expired');

		  $blueprint->softDeletes();
		  $blueprint->nullableTimestamps();
		});
	}

	public static function createUsersTable(\Unika\Application $app ,\Illuminate\Database\Schema\Builder $schema)
	{
		return $schema->create($app->config('auth.drivers.database.users_table'),function($blueprint)
		{
		  $blueprint->integer('id',True,True);
		  $blueprint->string('firstname');
		  $blueprint->string('lastname')->nullable();
		  $blueprint->string('username');
		  $blueprint->string('primary_email');
		  $blueprint->string('pass');
		  $blueprint->string('salt',128);
		  
		  $blueprint->tinyInteger('active');

		  $blueprint->dateTime('last_login')->nullable();
		  $blueprint->tinyInteger('last_failed_count')->nullable();
		  $blueprint->integer('role_id');

		  $blueprint->softDeletes();
		  $blueprint->nullableTimestamps();
		});		
	}
}