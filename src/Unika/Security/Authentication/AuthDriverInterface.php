<?php 
/**
 *	This file is part of the UnikaCMF project
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika\Security\Authentication;

interface AuthDriverInterface
{
	/**
	 *	@param array or AuthUserInterface $credentials
	 *	@return AuthUserInteface or throw AuthException if failure
	 *	@throw AuthException
	 */
	public function authenticate(array $credentials);

	/**
	 *	check if user is on blocked
	 *
	 *	@param array $credentials
	 *	@return True on blocked , null if credential not found
	 */
	public function isBlocked(array $credentials);
}