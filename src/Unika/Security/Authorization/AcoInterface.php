<?php
/**
 *	This file is part of the Unika-CMF project.
 *	ACO(Access Control Object) Interface
 *	
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Security\Authorization;

interface Aco
{

	/**
	 *
	 *	@param $permissions PermissionInterface or array of Permission interface
	 *	implementor must throw AuthorizationException if supplied parameters not valid Permission
	 */
	public function addPermission(PermissionInterface $permission);

	/**
	 *
	 *	return name of this Aco
	 */
	public function getAcoName();

	/**
	 *
	 *	return unique identifier of this aco
	 */
	public function getAcoIdentifier();

	/**
	 *
	 *	return description of this aco
	 */
	public function getAcoDescription();
}