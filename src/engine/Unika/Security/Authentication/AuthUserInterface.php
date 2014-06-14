<?php
/**
 *	This file is part of the Unika-CMF project.
 *	User must implement this interface to be use by Auth service
 *
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Security\Authentication;

interface AuthUserInterface
{
	//it can be username/email or whatever
	public function getAuthIdentifier();

	public function getPassword();

	public function getSalt();
}