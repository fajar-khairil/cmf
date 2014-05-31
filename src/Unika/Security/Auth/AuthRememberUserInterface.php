<?php
/**
 *	Unika-CMF Project
 *	User must implement this interface to be use by Auth service
 *
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Security\Auth;

interface AuthRememberUserInterface
{
	public function getRememberMeToken();

	public function getRememberMeTokenName();
}