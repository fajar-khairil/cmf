<?php
/**
 *	This file is part of the Unika-CMF project.
 *	Acl Default Implementation
 *	
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Security\Eloquent;

use Unika\Security\Authorization\AroInterface;

class Acl implements AclInterface 
{
	/**
	 *
	 *	is aro allowed to access aco?
	 *	@return boolean
	 */
	public function isAllowed(AroInterface $aro,AcoInterface $aco)
	{
		throw new \RuntimeException('not yet implemented');
	}

	/**
	 *
	 *	grant aro to aco
	 *	@return boolean
	 */

	public function grant(AroInterface $aro,AcoInterface $aco)
	{
		throw new \RuntimeException('not yet implemented');
	}
	
	/**
	 *
	 *	deny aro to aco
	 *	@return boolean
	 */
	public function deny(AroInterface $aro,AcoInterface $aco)
	{
		throw new \RuntimeException('not yet implemented');	
	}
}