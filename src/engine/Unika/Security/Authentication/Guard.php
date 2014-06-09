<?php
/**
 *	Unika-CMF Project
 *	Authentication Guard , prevent brute force attack and other security issues to Authentication
 *	
 *	@license MIT
 *	@author Fajar Khairil
 *
 */


namespace Unika\Security\Authentication;

use Unika\Security\Authentication\AuthUserGuardInterface;

class Guard
{
	protected $app;

	public function __construct(\Application $app)
	{
		$this->app = $app;
	}

	public function RegisterListener()
	{
		$auth = $this->app['auth'];

		$auth->onTokenMismatch(function($request,$user){
			$user = $payload['user'];
			$capsule = $this->app['capsule'];
			$user_table = $this->app['config']['auth.Eloquent.user_table'];

			if(  (int)$user['last_failed_count'] >= (int)$this->app['config']['auth.max_failed_attempt'] )
			{
				return $capsule::table($user_table)
					->where('id',$user['id'])
					->update(array('active' => 0 ));	
			}
			else
			{
				return $capsule::table($user_table)
				->where('id',$user['id'])
				->update(array('last_failed_count' => (int)$user['last_failed_count']+1 ));				
			}
		});
		
		$auth->onTokenAltered(function($request){		
			$capsule = $this->app['capsule'];

			$row = $capsule::table('banneds')
				->select('*')
				->where('ip_address',$request->getClientIp())
				->take(1)->get();

			if( !empty($row) )
			{
				return $capsule::table('banneds')
					->where('ip_address',$request->getClientIp())
					->update(array(
					'ip_address'	=> $request->getClientIp(),
					'failed_count' => (int)$row[0]['failed_count']+1,
					'reason'	   => 'token altered'
				));	
			}
			else
			{
				return $capsule::table('banneds')
				->insert(array(
					'ip_address'	=> $request->getClientIp(),
					'failed_count' => (int)$row[0]['failed_count']+1,
					'reason'	   => 'token altered',
					'created_at'	=> date('Y-m-d H:i:s',time())
				));				
			}
		});

		/*$auth->onTokenInvalid($listener, $priority = 0);

		$auth->onBadCredentials($listener, $priority = 0);

		$auth->onBadPassword($listener, $priority = 0);

		$auth->onAttemptSuccess($listener, $priority = 0);

		$auth->beforeAttempt($listener, $priority = 0);

		$auth->afterAttempt($listener, $priority = 0);*/
	}
}