<?php
/**
 *	This file is part of the Unika-CMF project.
 *	
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Security\Authorization\Driver\Database;

use Unika\Security\Authorization\AclDriverInterface;
use Unika\Application;

class AclRegistry implements AclDriverInterface
{

	/**
	 *	@param string roleName
	 *	@param string resourceName
	 *	@param string operation name
	 *	@return boolean
	 */
	public function queryAcl($role,$resource,$operation = null)
	{
		throw new \RuntimeException('Unika\Security\Authorization\Driver\Database\AclRegistry::queryAcl not yet impelemented');
	}

	/**
	 *	@param integer $roleId
	 *	@param integer $resourceId
	 *	@param array $operations array of operations
	 *	@param boolean $allow should allow supplied role to given resource and operation?
	 *	@return boolean True on success Throw Exception Otherwirse
	 */
	public function setRules($roleId,$resourceId,array $operations = array('*'),$allow = True)
	{
		throw new \RuntimeException('Unika\Security\Authorization\Driver\Database\AclRegistry::setRules not yet impelemented');
	}
}