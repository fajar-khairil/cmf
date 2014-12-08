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

	public function add(RoleInterface $role);

	//return RoleInterface
	public function createRole(array $attributes = array());

	public function remove($roleId);

	public function removeAll();

	public function has($roleId);

	public function all();

	public function get($roleId);
}