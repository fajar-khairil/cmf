<?php
/**
 *	This file is part of the Unika-CMF project.
 *	Acl Interface
 *	
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Security\Authorization;

interface AclInterface 
{

	/**
	 *
	 *	is aro allowed to access aco?
	 *	@return boolean
	 */
	public function isAllowed(AroInterface $aro,AcoInterface $aco);

	/**
	 *
	 *	grant aro to aco
	 *	@return boolean
	 */

	public function grant(AroInterface $aro,AcoInterface $aco);
	
	/**
	 *
	 *	deny aro to aco
	 *	@return boolean
	 */
	public function deny(AroInterface $aro,AcoInterface $aco);	
}