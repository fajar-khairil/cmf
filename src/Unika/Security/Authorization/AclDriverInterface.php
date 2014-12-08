<?php
/**
 *	This file is part of the Unika-CMF project.
 *	AclDriverInterface
 *
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika\Security\Authorization;

interface AclDriverInterface
{
	public function queryAcl($roleId,$resource,$operation = null);

	/**
	 *
	 *	@param integer roleId
	 *	@param integer resourceId
	 *	@param array operations array of operations
	 *	@param boolean should allow supplied role to given resource and operation?
	 */
	public function setRules($roleId,$resourceId,array $operations = array('*'),$allow);
}