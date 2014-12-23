<?php 
/**
 *	This file is part of the UnikaCMF project
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika\Security\Authentication;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Auth 
{
	protected $session;
	protected $authDriver;
	protected $authUserClass;

	protected $app = NULL;

	/**
	 *
	 *	@param AuthDriverInterface $authDriver Authentication Driver instance 
	 *  @param SessionInterface $session Session instance to use
	 *	@param string $AuthUserClass class of AuthUserInterface
	 */
	public function __construct(AuthDriverInterface $authDriver,SessionInterface $session)
	{
		$this->session = $session;
		$this->authDriver = $authDriver;
	}

	public function setAuthUserClass($authUserClass = null)
	{
		if( class_exists($authUserClass) AND \Unika\Util::classImplements($authUserClass,'AuthUserInterface') )
		{
			$this->authUserClass = $authUserClass;
		}
	}

	public function getAuthUserClass()
	{
		if( $this->authUserClass === null ){
			$this->authUserClass = 'SimpleAuthUserClass';
		}

		return $this->authUserClass;
	}

	public function setApplication(\Unika\Application $app)
	{
		$this->app = $app;
	}

	public function getApplication()
	{
		if( $this->app === NULL ){
			throw new \RuntimeException('Application not set, please set it via '.get_class($this).'::setApplication method.')
		}
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
	 *	@param mixed $credentials it can be array or AuthUserInterface
	 *  @param boolean $remember remember this user
	 *	@param integer $timeout take effect if $remember is True , if null defaultRememberTimeout will be used
	 *
	 *	@return boolean
	 */
	public function login($credentials,$remember = False,$timeout = null)
	{

	}

	/**
	 *	logout current session
	 */
	public function logout()
	{

	}

	/**
	 *	Validate credentials without login
	 *
	 *	@param mixed $credentials
	 *	@return boolean
	 */
	public function validate($credentials)
	{

	}

	/**
	 *	check if there is a logged in user
	 *
	 *	@return boolean
	 */
	public function check()
	{

	}

	/**
	 *	login user only for single request
	 *	@param mixed $credentials
	 *	@return boolean 
	 */
	public function once($credentials)
	{

	}

	/**
	 *	force given user/credential to login
	 *	@param mixed $user array of credentials or AuthUserInterface 
	 *	@return boolean
	 */
	public function forceLogin($user)
	{

	}

	/**
	 *	login user only for single request
	 *	@param mixed $credentials
	 *	@return AuthUserInterface | NULL
	 */
	public function user()
	{

	}

	/**
	 *	is current logged in user logged via remembered ?
	 *	@return boolean
	 */
	public function viaRemember()
	{

	}
}