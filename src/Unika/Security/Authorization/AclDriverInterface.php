<?php
/**
 *	This file is part of the UnikaCMF project.
 *	AclDriverInterface
 *
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika\Security\Authorization;

interface AclDriverInterface
{
	/**
	 *	@param string roleName
	 *	@param integer resourceName
	 *	@param string operation name
	 *	@return boolean
	 */
	public function queryAcl($roleId,$resource,$operation = null);

	/**
	 *	@param integer $roleId
	 *	@param integer $resourceId
	 *	@param array $operations array of operations
	 *	@param boolean $allow should allow supplied role to given resource and operation?
	 *	@return boolean True on success Throw Exception Otherwirse
	 */
	public function setRules($roleId,$resourceId,array $operations = array('*'),$allow = True);
}