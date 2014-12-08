<?php
/**
 *	This file is part of the Unika-CMF project.
 *	Role Interface
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika\Security\Authorization;

Interface RoleRegistryInterface{

	public function addRole(array $role);

	//return RoleInterface
	public function createRole(array $attributes);

	public function removeRole($roleId);

	public function hasRole($roleId);

	public function allRole();

	public function getRole($roleId);
}