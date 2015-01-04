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
	 *	@return AuthUserInteface
	 *	@throw AuthException
	 */
	public function authenticate($credentials);
}