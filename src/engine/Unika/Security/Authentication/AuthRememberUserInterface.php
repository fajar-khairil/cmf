<?php
/**
 *	This file is part of the Unika-CMF project.
 *	User must implement this interface to be use by Auth service
 *
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Security\Authentication;

interface AuthRememberUserInterface
{
	public function getRememberMeToken();
}