<?php
/**
 *	This file is part of the Unika-CMF project.
 *	Authentication Guard , prevent brute force attack and other security issues to Authentication
 *	
 *	@license MIT
 *	@author Fajar Khairil
 *
 */

namespace Unika\Security\Authentication;

class AuthGuard
{
	protected $app;

	public function __construct(\Application $app)
	{
		$this->app = $app;
	}

	public function RegisterListener()
	{
		$auth = $this->app['auth'];
		$self = $this;

		$auth->onTokenMismatch(function($request,$user) use($self){
			$self->doUpdateUser($user);
		});		
		
		$auth->onTokenAltered(function($request) use($self){		
			$self->doInvalidToken($request,'Remember Token Altered');
		});

		$auth->onTokenInvalid(function($request) use($self){
			$self->doInvalidToken($request,'Invalid Remember Token');
		});

		$auth->onBadCredentials(function($request) use($self)
		{
			$self->doInvalidToken($request,'Bad Credentials');
		});

		$auth->onBadPassword(function($user,$request) use($self){
			$self->doUpdateUser($user);
		});

		$auth->beforeAttempt(function($credentials)
		{
			$capsule = $this->app['capsule'];

			//normalize credentials
			if( isset($credentials['password']) ){
				$credentials['pass'] = $credentials['password'];
				unset($credentials['password']);
			}	
			if( isset($credentials['email']) ){
				$credentials['username'] = $credentials['email'];
				unset($credentials['email']);
			}

			$row = $capsule::table($this->app['config']['auth.Eloquent.user_table'])
			->where('username',$credentials['username'])
			->take(1)
			->get();	

			if( !empty($row) )
			{
				if( (int)$row[0]['last_failed_count'] >= (int)$this->app['config']['auth.max_failed_attempt'] )
				{
					$this->app->abort(403,'user blocked.');
				}
			}				
		});
	}

	/**
	 *
	 *	check if the given ip is blocked
	 *
	 *	@return boolean
	 */
	public function isBlocked($ip_address)
	{
		$capsule = $this->app['capsule'];
		$row = $capsule::table('bans')
		->where('ip_address',$ip_address)
		->take(1)
		->get();
		
		if( !empty($row) )
		{
			if( (int)$row[0]['failed_count'] >= (int)$this->app['config']['auth.max_failed_attempt'] )
				return True;
			else
				return False;
		}
		else
		{
			return False;
		}
	}

	protected function doInvalidToken($request,$reason)
	{
		$capsule = $this->app['capsule'];

		$row = $capsule::table('bans')
			->select('*')
			->where('ip_address',$request->getClientIp())
			->take(1)->get();

		if( !empty($row) )
		{
			$capsule::table('bans')
				->where('ip_address',$request->getClientIp())
				->update(array(
				'ip_address'	=> $request->getClientIp(),
				'failed_count' => (int)$row[0]['failed_count']+1,
				'reason'	   => $reason
			));	
		}
		else
		{
			$capsule::table('bans')
			->insert(array(
				'ip_address'	=> $request->getClientIp(),
				'failed_count' => (int)$row[0]['failed_count']+1,
				'reason'	   => $reason,
				'created_at'	=> date('Y-m-d H:i:s',time())
			));				
		}

	}

	protected function doUpdateUser($user)
	{
		$capsule = $this->app['capsule'];
		$user_table = $this->app['config']['auth.Eloquent.user_table'];
		$query = $capsule::table($user_table)
					->where('id',$user['id']);

		if(  (int)$user['last_failed_count'] >= (int)$this->app['config']['auth.max_failed_attempt'] )
			$query->update(array('active' => 0 ));	
		else
			$query->update(array('last_failed_count' => (int)$user['last_failed_count']+1));				
	}
}