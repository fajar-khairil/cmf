<?php 
/**
 *	This file is part of the UnikaCMF project
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika\Security\Authentication;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Unika\Application;

class Auth 
{
	protected $session;
	protected $authDriver;
	protected $defaultTimeout = 30;
	protected $failureReason = null;
	protected $sessionName = "app_sess";
	protected $app = null;
	protected $cache = null;
	protected $viaRemember = False;

	/**
	 *
	 *	@param AuthDriverInterface $authDriver Authentication Driver instance 
	 *  @param SessionInterface $session Session instance to use
	 */
	public function __construct(Application $app ,AuthDriverInterface $authDriver,SessionInterface $session)
	{
		$this->app = $app;
		$this->session = $session;
		$this->authDriver = $authDriver;
	}

	public function driver()
	{
		return $this->authDriver;
	}

	public function getSession()
	{
		return $this->session;
	}

	public function setCache(\Illuminate\Cache\Repository $cache)
	{
		$this->cache = $cache;
	}

	public function getCache()
	{
		if( null === $this->cache ){
			throw new \RuntimeException('Application not set, please set it via '.get_class($this).'::setCache method.');
		}

		return $this->cache;
	}

	public function setAuthSessionName($sessionName)
	{
		$this->sessionName = $sessionName;
	}

	public function getAuthSessionName()
	{
		if( !$this->sessionName )
			$this->sessionName = $this->app->config('auth.session_name');
		return $this->sessionName;
	}

	/**
	 *	@param integer $minutes take effect if login with remember True, give it -1 to remember forever 
	 *
	 *	@return void
	 */
	public function setDefaultRememberTimeout($minutes = 30)
	{
		$this->defaultTimeout = $minutes;
	}

	public function getDefaultRememberTimeout()
	{
		return $this->defaultTimeout;
	}

	/**
	 *	@param mixed $credentials it can be array
	 *  @param boolean $remember remember this user
	 *	@param integer $timeout take effect if $remember is True , if null defaultRememberTimeout will be used
	 *
	 *	@return boolean
	 */
	public function login($credentials,$remember = False,$timeout = null)
	{	
		// resolve valid credential column
		$col = 'username';
		if( !isset($credentials[$col]) ){
			$col = 'email';
			if( !isset($credentials[$col]) )
				$this->app['logger']->addCritical('FATAL : Incorrect credentials supplied.');
				throw new AuthException('FATAL : Incorrect credentials supplied.');
		}

		if( False !== strpos($credentials[$col], '@') ){
			$credentials['email'] = $credentials[$col];
			$col = 'email';
		}

		$this->app['Illuminate.events']->fire('auth.beforeLogin',[$credentials,$col]);
		
		if( $this->app->config('auth.guard.active') )
		{
			if( $this->authDriver->isBlocked($credentials,$col) )
			{
				$this->failureReason = 'this account on Blocked.';
				return False;
			}
		}

		$user = $this->internalAuthenticate($credentials,$col);

		if( $user )
		{
			$this->session->set($this->sessionName,$user);
			$this->app['Illuminate.events']->fire('auth.success',[$credentials,$col,$remember,$timeout,$this]);

			if( True === (bool)$remember )
			{
				if( !is_numeric($timeout) )
					$timeout = $this->getDefaultRememberTimeout();

				// nesbot\carbon
				$carbon = new \Carbon\Carbon( \Carbon\Carbon::now() );
				$carbon->addMinutes($timeout);

				$token = \Unika\Security\Util::generateRandomString(32);

				//** remember me */
				$this->app->after(function($request,$response,$app) use($user,$timeout,$token,$carbon)
				{
					$response->headers->setCookie(new Cookie(
						$app->config('auth.remember_me.cookie_name','auth_remember'),
						json_encode(['id' => $user['id'],'scrt' => $token]),
						$carbon
					));
				});

				$this->authDriver->setRememberMeToken($user['id'],$token,$carbon->toDateTimeString());
			}
			return True;
		}
		else
		{
			$this->app['Illuminate.events']->fire('auth.failure',[$credentials,$col]);

			return False;
		}
	}

	/**
	 *
	 *	Register the user
	 */
	public function register(array $user)
	{
		if( !isset($user['password']) )
			throw new AuthException('password not supplied.');

		$user['salt'] = $this->app['sec.util']->generateRandomString(8);
		$user['pass'] = $this->app['sec.util']->createPasswordHash($user['password'].$salt);
		$user['created_at']	= date('Y-m-d H:i:s');

		unset($user['password']);
		return $this->authDriver->register($user);
	}

	/**
	 *	@return AuthUser on Success False or Exception on Failure
	 */
	protected function internalAuthenticate($credentials,$col)
	{	
		try
		{
			if( null === array_get($credentials,'password') ){
				$this->failureReason = 'no password supplied';
				return False;
			}

			// resolve user
			$user = $this->authDriver->resolveUser($col,$credentials[$col]);

			if( !$user )
			{
				// @todo : should we localized Exception Message ?
				throw new AuthException('Invalid Username supplied');
			}

			$passwordLibClass = $this->app->config('auth.password_hasher_class');
		
			if( !\Unika\Util::classImplements($passwordLibClass,'Unika\Security\PasswordHasherInterface') )
			{
				throw new AuthException('Fatal Error : invalid password_hasher_class please check your auth config.');
			}

			$passwordLib = new $passwordLibClass();

			$isValidPassword = $passwordLib->verifyPasswordHash( $credentials['password'].$user['salt'],$user['pass'] );

			if( !$isValidPassword )
			{
				throw new AuthException('invalid password supplied.');
			}

			if( $user )
			{				
				$this->resetFailureReason();
				return $user;
			}
		}
		catch(\Exception $e)
		{	
			$this->failureReason = $e->getMessage();
			return False;
		}
	}

	protected function resetFailureReason()
	{
		return $this->failureReason = null;
	}

	public function getFailureReason()
	{
		return $this->failureReason;
	}

	/**
	 *	logout current session
	 */
	public function logout()
	{
		$user = $this->session->get($this->sessionName);
		$this->session->remove($this->sessionName);
		$this->app['Illuminate.events']->fire('auth.logout',[$this]);


		//** remember me , on logout remove remember cookie for this user */
		$this->app->after(function($request,$response,$app)
		{
			$response->headers->clearCookie($app->config('auth.remember_me.cookie_name','auth_remember'));
		});
	}

	/**
	 *	Validate credentials without login
	 *
	 *	@param mixed $credentials
	 *	@return boolean
	 */
	public function validate($credentials)
	{
		return (boolean)$this->internalAuthenticate($credentials);
	}

	/**
	 *	check if there is a logged in user
	 *
	 *	@return boolean
	 */
	public function check()
	{
		$valid = $this->session->has($this->sessionName);
		if( !$valid )
		{
			$raw_cookie = $this->app['request_stack']->getCurrentRequest()->cookies->get($this->app->config('auth.remember_me.cookie_name','auth_remember'));
			$cookie = json_decode( $raw_cookie,True );

			if( is_array( $cookie ) )
			{
				if( True === $this->authDriver->checkRememberMeToken($cookie['id'],$cookie['scrt']) )
				{
					$this->viaRemember = True;
					$valid = True;
				}
			}
		}

		return $valid;
	}

	/**
	 *	login user only for single request
	 *	@param mixed $credentials
	 *	@return boolean 
	 */
	public function once(array $credentials)
	{
		$user = $this->internalAuthenticate($credentials);

		if( $user )
		{
			$this->session->set($this->sessionName,$user);
			$sessionName = $this->sessionName;

			$this->app->after(function($request,$response,$app)use($sessionName){
				$app['session']->remove($sessionName);
			});
			
			return True;
		}
		else
		{
			return False;
		}
	}

	/**
	 *	force given userId to login
	 *	@param integer | string $userId
	 *	@return boolean
	 */
	public function forceLogin($userId)
	{
		$user = $this->authDriver->resolveUser('id', $userId);

		if( $user )
		{
			$this->session->set($this->sessionName,$user);
			return True;
		}
		else
		{
			return False;
		}
	}

	/**
	 *	login user only for single request
	 *	@param mixed $credentials
	 *	@return array | NULL
	 */
	public function user($attrName = null,$default = null)
	{
		if( $this->check())
		{
			if( is_string($attrName) ){
				$user = $this->session->get($this->sessionName);
				return isset($user[$attrName]) ? $user[$attrName] : $default;
			}else{
				return $this->session->get($this->sessionName);
			}
		}
		else
			return null;
	}

	/**
	 *	is current logged in user logged via remembered ?
	 *	@return boolean
	 */
	public function viaRemember()
	{
		return $this->viaRemember;
	}
}