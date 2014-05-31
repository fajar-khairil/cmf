<?php
/**
 *	Unika-CMF Project
 *	default Auth Service Implementation
 *	
 *	@license MIT
 *	@author Fajar Khairil
 */
namespace Unika\Security\Authentication;

class Auth implements AuthInterface
{
	protected $app;

	public function __constructor(\Unika\Application $app)
	{
		$this->app = $app;
	}

	/**
	 *
	 *	login attempt
	 *
	 *	@param $credentials array, ussualy its username and password but it can be anything
	 * 	@return boolean
	 * 	throw AuthException if failed
	 */
	public function attempt(array $credentials ,$remember = False,$expired = 0)
	{
		
	}

	/**
	 *
	 *	allows you to validate a user's credentials without actually logging them into the application
	 *
	 *	@return boolean
	 */
	public function validate(array $credentials)
	{

	}

	/**
	 *
	 *	single request login no session or cookie
	 */
	public function once(array $credentials)
	{

	}

	//determine if user already loggedin
	public function check()
	{

	}

	//logout user
	public function logout()
	{

	}

	//login user manually
	public function forceLogin(AuthUserInterface $user)
	{

	}

	/**
	 *
	 *	@return User Object null if user not already loggedin
	 */
	public function user()
	{

	}
}