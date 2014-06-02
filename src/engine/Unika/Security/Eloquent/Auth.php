<?php
/**
 *	Unika-CMF Project
 *	default Auth Service Implementation
 *	
 *	@license MIT
 *	@author Fajar Khairil
 *
 */

namespace Unika\Security\Eloquent;

use Unika\Security\Authentication\AuthInterface;
use Unika\Security\Authentication\AuthUserInterface;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder as hash;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Auth implements AuthInterface
{
	protected $app;
	protected $sign_method = AuthInterface::VIA_NORMAL_LOGIN;

	public function __construct(\Application $app)
	{
		$this->app = $app;
	}

	/**
	 *
	 *	login attempt
	 *
	 *	@param $credentials array, ussualy its username and password but it can be anything
	 * 	@return boolean
	 */
	public function attempt(array $credentials , $remember = False, $expired = null )
	{
		if( $this->check() === True )
		{
			$this->app['session']->migrate();
			return True;
		}

		$result = $this->_checkCredentials( $credentials );

		if( $result )
		{
			return $this->sign($result,AuthInterface::VIA_NORMAL_LOGIN,$remember,$expired);
		}

		return (boolean)$result;	
	}

	/**
	 *	store to session
	 */
	protected function sign($user, $signMethod,$remember = False, $expired = null)
	{
		unset($user['pass']);unset($user['salt']);
		$this->app['session']->set( $this->app['config']['auth.session_key'] , $user );

		if( $remember === True )
		{
			//set remember cookie
			$cookies = $this->app['config'][ 'cookie.'.$this->app['config']['auth.cookie_remember'] ];

			if( is_int($expired) )
				$cookies['expired'] = $expired;
			
			$cookies['expired'] = time()+$cookies['expired'];

			$request = $this->app['request'];

			$secret_data = serialize(array(
				'_token'		=>	$this->app['PasswordLib']->getRandomToken(32),
				'user_id'		=>	$user['id'],
				'ip_address'	=>	$request->getClientIp()
			));

			$cookies['value'] = $this->app['security.util']->encrypt( $secret_data );

			$remember_cookie = $this->app['cookie']->cookie($cookies);

			//update user
			$this->_updateUser($user['id'],[
				'remember_token'		=>	$cookies['value'],
				'last_failed_count'		=>  0
			]);			

			$this->app->after(function($request,$response) use($remember_cookie){
				$response->headers->setCookie( $remember_cookie );
			});


			if( ($this->app['config']['auth.restrict_ip'] === True) OR ($this->app['config']['auth.enabled_session_info'] === True) )
			{
				//prepare values
				$values = array(
					'session_token'		=>	$this->app['session']->getId(),
					'user_agent'		=>	$request->headers->get('user-agent'),
					'ip_address'		=>	$request->getClientIp(),
					'remember_token'	=>	$cookies['value'],
					'session_time'		=>	time()
				);
				$this->_updateSessionInfo($values);				
			}

			$this->sign_method = $signMethod;
		}

		return True;
	}

	/**
	 *
	 *	update user row
	 */
	protected function _updateUser($id,array $values)
	{
		//if return is False log
		$capsule = $this->app['capsule'];
		$values['last_login'] = date('Y-m-d H:i:s',time());
		return $capsule::table($this->app['config']['auth.Eloquent.user_table'])
				->where('id',$id)
				->update($values);	
	}

	/**
	 *
	 *	internal function check supplied credentials
	 *	@return user array if success return False if failed, use by validate and attempt
	 */
	protected function _checkCredentials( array $credentials )
	{
		$capsule = $this->app['capsule'];
		$query = $capsule::table($this->app['config']['auth.Eloquent.user_table'])
				->select('*');

		if( isset($credentials['password']) ){
			$credentials['pass'] = $credentials['password'];
			unset($credentials['password']);
		}	

		$pass = $credentials['pass'];unset($credentials['pass']);

		foreach( $credentials as $col=>$value )
		{
			$query->where($col,$value);
		}

		$row = $query->take(1)->get();

		$result = False;

		if( !empty($row) )
		{	
			if( $this->app['PasswordLib']->verifyPasswordHash($pass.$row[0]['salt'],$row[0]['pass']) )
				$result = $row[0];
		}

		return $result;
	}

	/**
	 *	Internal function insert|update session info if restrict_ip === True
	 */
	protected function _updateSessionInfo(array $values)
	{
		$session_info_table = $this->app['config']['auth.session_info_table'];

		$capsule = $this->app['capsule'];
		$qGet = $capsule::table($session_info_table)
				->select('*')
				->where('session_token',$values['session_token'])
				->get();		

		if( count($qGet) > 0 )
		{
			$capsule::table($this->app['config']['auth.session_info_table'])
				->where('session_token',$values['session_token'])
				->update($values);	
		}
		else
		{
			$qInsert = $capsule::table($this->app['config']['auth.session_info_table'])
					->insert($values);
		}
	}

	/**
	 *
	 *	allows you to validate a user's credentials without actually logging them into the application
	 *
	 *	@return boolean
	 */
	public function validate(array $credentials)
	{
		return (boolean)$this->_checkCredentials($credentials);
	}

	/**
	 *
	 *	single request login no session or cookie
	 *	AuthInterface::VIA_ONCE
	 *	@return boolean
	 */
	public function once(array $credentials)
	{
		//this implementation still wrong , i need to implement flash_session
		/*$result = $this->_checkCredentials($credentials);
		if( $result )
			$this->app['session']->getFlashBag()->set('test',$result);

		return (boolean) $result;*/

		throw new \RuntimeException('not yet implemented');
	}

	/**
	 *
	 *	determine if user already loggedin
	 *	@return boolean
	 */
	public function check()
	{
		return (boolean)$this->app['session']->has($this->app['config']['auth.session_key']);
	}

	//logout user
	public function logout()
	{
		return $this->app['session']->remove($this->app['config']['auth.session_key']);
	}

	//login user manually
	public function forceLogin(array $user)
	{
		if( $this->check() === True )
			return True;

		return $this->sign($user,AuthInterface::VIA_FORCE_LOGIN);
	}

	/**
	 *
	 *	@return array of user property and return null if user not already loggedin
	 */
	public function user()
	{
		return $this->app['session']->get($this->app['config']['auth.session_key']);
	}

	/**
	 *	valid return value see AuthInterface CONST
	 *	@return mixed
	 */
	public function signinMethod()
	{
		return $this->sign_method;
	}
}