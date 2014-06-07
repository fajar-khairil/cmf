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
use Unika\Security\Authentication\AuthException;
use Unika\Security\Authentication\AuthUserInterface;
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
		if( $this->check() ) return True;
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

			$remember_token = $this->app['PasswordLib']->getRandomToken(32);
			$secret_data = serialize(array(
				'remember_token' => $remember_token,
				'user_id'		=> $user['id']
			));

			$cookies['value'] = $this->app['signer']->sign( base64_encode($secret_data) );

			$remember_cookie = $this->app['cookie']->cookie($cookies);

			//update user
			$this->_updateUser($user['id'],[
				'last_login'			=> date('Y-m-d H:i:s',time()),
				'remember_token'		=>	$remember_token,
				'last_failed_count'		=>  0
			]);			

			$this->app->after(function($request,$response) use($remember_cookie){
				//dd('here');
				$response->headers->setCookie( $remember_cookie );
			});

			if( ($this->app['config']['auth.restrict_ip'] === True) OR ($this->app['config']['auth.enabled_session_info'] === True) )
			{
				//prepare values
				$values = array(
					'session_token'		=>	$this->app['session']->getId(),
					'user_agent'		=>	$request->headers->get('user-agent'),
					'ip_address'		=>	$request->getClientIp(),
					'remember_token'	=>	$remember_token,
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
			{
				$result = $row[0];
			}
			else
			{
				$this->_updateUser($row[0]['id'],['last_failed_count' => (int)$row[0]['last_failed_count']+1]);
			}
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
		if( $this->check() ) return True;

		$result = $this->_checkCredentials($credentials);
		if( $result )
		{
			$this->app['session']->getFlashBag()->set($this->app['config']['auth.session_key'],$result);
			$this->sign_method = AuthInterface::VIA_ONCE;
		}

		return (boolean) $result;
	}

	/** 
	 *	check if remember_me cookie is present
	 *
	 *	@param $asUser if True return value will be user
	 *	@return mixed
	 */
	protected function checkRemembermePresent($asUser = False)
	{
		$cookies = $this->app['config'][ 'cookie.'.$this->app['config']['auth.cookie_remember'] ];
		$request = $this->app['request'];
		$result = $request->cookies->has($cookies['name']);
		
		if( !$result ) return False;
		
		$result = $this->app['signer']->check($request->cookies->get($cookies['name']));
			

		if( $result === False ){
			throw new AuthException('token was broken');
		}

		if( $result )
		{
			$tokens = $this->app['security.util']->extractSign($request->cookies->get($cookies['name']));

			$secret_data = unserialize(base64_decode($tokens['secret_data']));
		
			$capsule = $this->app['capsule'];

			$session_info = $capsule::table($this->app['config']['auth.session_info_table'])
							->select('*')
							->where('remember_token',$secret_data['remember_token'])
							->take(1)
							->get();

			if( empty($session_info) ) return False;				
				
			//compare user_agent and and ip_address
			if( 
				( $request->headers->get('user-agent') == $session_info[0]['user_agent'] ) 
				AND 
				( $request->getClientIp() == $session_info[0]['ip_address'] )
			)
			{						
				$user = $capsule::table($this->app['config']['auth.Eloquent.user_table'])
						->select('*')
						->where('id',$secret_data['user_id'])
						->take(1)->get();

				if( !empty($user) AND $user[0]['remember_token'] == $session_info[0]['remember_token'] )
				{
					$result = ( $asUser === True ) ? $user[0] : (boolean)$user;
				}
				else
				{
					return False;
				}
			}
		}		

		return $result;
	}

	/**
	 *
	 *	determine if user already loggedin
	 *	@return boolean
	 */
	public function check()
	{
		$result = $this->app['session']->has($this->app['config']['auth.session_key']);
		
		if( $result === False )
		{
			$user = $this->checkRemembermePresent(True);

			if( is_array($user) ){
				$this->app['session']->set($this->app['config']['auth.session_key'],$user);
				$result = True;
			}
		}

		return (boolean)$result;
	}

	//logout user
	public function logout()
	{
		$result = $this->app['session']->remove($this->app['config']['auth.session_key']);
		$this->app['session']->invalidate();		
		
		$cookies = $this->app['config'][ 'cookie.'.$this->app['config']['auth.cookie_remember'] ];
		$this->app->after(function($request,$response) use($cookies){
			$response->headers->clearCookie( $cookies['name'],$cookies['path'],$cookies['domain'] );
		});		
		
		return $result;
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