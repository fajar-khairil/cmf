<?php
/**
 *	This file is part of the Unika-CMF project.
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
	protected $dispatcher;
	protected $sign_method = AuthInterface::VIA_NORMAL_LOGIN;

	public function __construct(\Application $app)
	{
		$this->app = $app;
		$this->dispatcher = $this->app['Illuminate.events'];
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

		$this->dispatcher->fire('auth.before_attempt',['credentials' => $credentials]);

		$result = $this->_checkCredentials( $credentials );

		if( $result )
		{			
			$this->dispatcher->fire('auth.attempt_success',['user' => $result]);
			return $this->sign($result,AuthInterface::VIA_NORMAL_LOGIN,$remember,$expired);
		}
		
		$this->dispatcher->fire('auth.attempt_failed',array('auth' => $this, 'user' => $result));

		return (boolean)$result;	
	}

	/**
	 *	store to session
	 */
	protected function sign($user, $signMethod,$remember = False, $expired = null)
	{
		unset($user['pass']);unset($user['salt']);
		$this->app['session']->set( $this->app['config']['auth.session_key'] , $user );

		$user_values = array(
			'last_login'		=> date('Y-m-d H:i:s',time()),
			'last_failed_count'	=> 0
		);

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
			$user_values['remember_token']	 = $remember_token;	

			$this->app->after(function($request,$response) use($remember_cookie){
				$response->headers->setCookie( $remember_cookie );
			});

			//prepare values
			$values = array(
				'session_token'		=>	$this->app['session']->getId(),
				'user_agent'		=>	$request->headers->get('user-agent'),
				'ip_address'		=>	$request->getClientIp(),
				'remember_token'	=>	$remember_token,
				'session_time'		=>	time()
			);
			$this->_updateSessionInfo($values);				

			$this->sign_method = $signMethod;
		}

		$capsule = $this->app['capsule'];
		$values['last_login'] = date('Y-m-d H:i:s',time());
		$query = $capsule::table($this->app['config']['auth.Eloquent.user_table'])
				->where('id',$user['id'])
				->update($user_values);	

		return True;
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

		if( isset($credentials['email']) ){
			$credentials['username'] = $credentials['email'];
			unset($credentials['email']);
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
				if( $this->dispatcher->hasListeners('auth.bad_password') )
					$this->dispatcher->fire('auth.bad_password',['user' => $row[0], 'request' => $this->app['request']]);
				else
					throw new Symfony\Component\Security\Core\Exception\BadCredentialsException();
			}
		}
		else
		{				
			if( $this->dispatcher->hasListeners('auth.bad_credentials') )
				$this->dispatcher->fire('auth.bad_credentials',['request' => $this->app['request']]);
			else
				throw new \Symfony\Component\Security\Core\Exception\UsernameNotFoundException();
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
	 *	AuthInterface::VIA_ONCE
	 *	@return boolean
	 */
	public function once(array $credentials)
	{
		if( $this->check() ) return True;

		$result = $this->_checkCredentials($credentials);
		if( $result )
		{
			$this->sign($result,AuthInterface::VIA_ONCE);
			$self = $this;
			$this->app->after(function($request,$response)use($self){
				$self->logout();
			});
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
			if( $this->dispatcher->hasListeners('auth.token_altered') ){
				$this->dispatcher->fire('auth.token_altered', ['request' => $this->app['request']]);
				return False;
			}
			else{
				throw new AuthException('token altered');					
			}
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

			if( empty($session_info) ){
				if( $this->dispatcher->hasListeners('auth.token_invalid') ){
					$this->dispatcher->fire('auth.token_invalid', ['request' => $this->app['request']]);
					return False;
				}
				else{
					throw new AuthException('invalid token');					
				}
			} 		
			
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
					if( $this->dispatcher->hasListeners('auth.token_mismatch') ){
						$this->dispatcher->fire('auth.token_mismatch', ['request' => $this->app['request'],'user' => $user[0]] );
						return False;			
					}
					else{
						throw new AuthException('token mismatch');	
					}
				}
			}
			else
			{		
				if( $this->dispatcher->hasListeners('auth.token_altered') ){
					$this->dispatcher->fire('auth.token_altered', ['request' => $this->app['request'],'misc' => 'pertamax' ]);
					return False;
				}
				else{
					throw new AuthException('token altered');					
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
		$this->dispatcher->fire('auth.before_logout',['auth' => $this]);
		$result = $this->app['session']->remove($this->app['config']['auth.session_key']);
		$this->app['session']->invalidate();		
		
		$cookies = $this->app['config'][ 'cookie.'.$this->app['config']['auth.cookie_remember'] ];
		$this->app->after(function($request,$response) use($cookies){
			$response->headers->clearCookie( $cookies['name'],$cookies['path'],$cookies['domain'] );
		});		
		
		$this->dispatcher->fire('auth.after_logout',['auth' => $this]);	
		return (boolean)$result;
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

	public function onTokenMismatch($listener, $priority = 0)
	{
		$this->dispatcher->listen('auth.token_mismatch',$listener,$priority);
	}

	public function onTokenAltered($listener, $priority = 0)
	{
		$this->dispatcher->listen('auth.token_altered',$listener,$priority);
	}

	public function onTokenInvalid($listener, $priority = 0)
	{
		$this->dispatcher->listen('auth.token_invalid',$listener,$priority);
	}

	public function onBadCredentials($listener, $priority = 0)
	{
		$this->dispatcher->listen('auth.bad_credentials',$listener,$priority);		
	}

	public function onBadPassword($listener, $priority = 0)
	{
		$this->dispatcher->listen('auth.bad_password',$listener,$priority);
	}

	public function onAttemptSuccess($listener, $priority = 0)
	{
		$this->dispatcher->listen('auth.attempt_success',$listener,$priority);
	}

	public function beforeAttempt($listener, $priority = 0)
	{
		$this->dispatcher->listen('auth.before_attempt',$listener,$priority);
	}

	public function onAttemptFailed($listener, $priority = 0)
	{
		$this->dispatcher->listen('auth.attempt_failed',$listener,$priority);
	}


	public function beforeLogout($listener, $priority = 0)
	{
		$this->dispatcher->listen('auth.before_logout',$listener,$priority);
	}

	public function afterLogout($listener, $priority = 0)
	{
		$this->dispatcher->listen('auth.after_logout',$listener,$priority);
	}
}