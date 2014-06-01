<?php
/**
 *	Unika-CMF Project
 *	Auth Service Interface
 *	
 *	@license MIT
 *	@author Fajar Khairil
 */
namespace Unika\Security\Authentication;

interface AuthInterface
{
	/**
	 *
	 *	login attempt
	 *
	 *	@param $credentials array, ussualy its username and password but it can be anything
	 * 	@return boolean
	 */
	public function attempt(array $credentials ,$remember = False/*,$restrict_ip = False*/,$expired = 0);

	/**
	 *
	 *	allows you to validate a user's credentials without actually logging them into the application
	 *
	 *	@return boolean
	 */
	public function validate(array $credentials);

	/**
	 *
	 *	single request login no session or cookie
	 */
	public function once(array $credentials);

	//determine if user already loggedin
	public function check();

	//logout user
	public function logout();

	//login user manually
	public function forceLogin(AuthUserInterface $user);

	/**
	 *
	 *	@return User Object null if user not already loggedin
	 */
	public function user();
}