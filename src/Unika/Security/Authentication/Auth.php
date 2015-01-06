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
	public function __construct(AuthDriverInterface $authDriver,SessionInterface $session)
	{
		$this->session = $session;
		$this->authDriver = $authDriver;
	}

	public function getSession()
	{
		return $this->session;
	}

	public function setApplication(\Unika\Application $app)
	{
		$this->app = $app;
	}

	public function getApplication()
	{
		if( $this->app === NULL ){
			throw new \RuntimeException('Application not set, please set it via '.get_class($this).'::setApplication method.');
		}

		return $this->app;
	}

	public function setCache(\Illuminate\Cache\Repository $cache)
	{
		$this->cache = $cache;
	}

	public function getCache()
	{
		if( $this->cache === NULL ){
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
			$this->sessionName = $this->getApplication()->config('auth.session_name');
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
		$this->getApplication()['Illuminate.events']->fire('auth.beforeLogin',[$credentials]);
		
		if( $this->getApplication()->config('auth.guard.active') )
		{
			if( $this->authDriver->isBlocked($credentials) )
			{
				$this->failureReason = 'this account on Blocked.';
				return False;
			}
		}

		$user = $this->internalAuthenticate($credentials);

		if( $user )
		{
			$this->session->set($this->sessionName,$user);
			$this->getApplication()['Illuminate.events']->fire('auth.success',[$credentials,$remember,$timeout,$this]);

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
			$this->getApplication()['Illuminate.events']->fire('auth.failure',[$credentials]);

			return False;
		}
	}

	/**
	 *	@return AuthUser on Success False or Exception on Failure
	 */
	protected function internalAuthenticate($credentials)
	{	
		try
		{
			$user = $this->authDriver->authenticate($credentials);

			if( $user )
			{
				
				$this->resetFailureReason();
				return $user;
			}
		}
		catch(\Exception $e)
		{	
			$this->failureReason = $e->getMessage();
			if( $this->getApplication() )
			{
				if( True === $this->getApplication()['debug'] )
				{
					throw $e;
				}
			}
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
		$this->getApplication()['Illuminate.events']->fire('auth.logout',[$this]);


		//** remember me , on logout remove remember cookie for this user */
		$this->app->after(function($request,$response,$app){
			
			if( !is_numeric($timeout) )
			{
				$timeout = $this->getDefaultRememberTimeout();
			}

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
	public function once($credentials)
	{
		throw new \RuntimeException(__FUNCTION__.'not yet implemented');
	}

	/**
	 *	force given user/credential to login
	 *	@param mixed $user array of credentials 
	 *	@return boolean
	 */
	public function forceLogin($user)
	{
		throw new \RuntimeException(__FUNCTION__.'not yet implemented');
	}

	/**
	 *	login user only for single request
	 *	@param mixed $credentials
	 *	@return array | NULL
	 */
	public function user()
	{
		if( $this->check() )
			return $this->session->get($this->sessionName);
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