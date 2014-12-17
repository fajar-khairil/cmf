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
	protected $acl_table;
	protected $app;

	public function __construct(Application $app)
	{
		$this->app = $app;
		$this->acl_table = $this->app->config('acl.Database.acl_table');
	}

	/**
	 *	@param string roleName
	 *	@param string resourceName
	 *	@param string operation name
	 *	@return boolean
	 */
	public function queryAcl($role,$resource,$operation = null)
	{	
		$acl = ORM::for_table($this->acl_table)->where(array(
			'role_id'	=> $role,
			'aco_id'	=> $resource
		))->find_one();

		$permissions = json_decode($acl->permissions,True);

		//should we throw an exception ?
		if( !$permissions ){ 
			throw new AclException('AclRegistry cannot decode permission.');
		}

		if( $permissions[0] == "*" ){ return True; }
		return in_array($operation, $permissions);
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
		$acl = ORM::for_table($this->acl_table)->where(array(
					'role_id'	=> $roleId,
					'aco_id'	=> $resourceId
		))->find_one();

		if( !$acl )
		{
			if( $allow === True )
			{
				// new fresh acl rule
				$acl = ORM::for_table($this->acl_table)->create(array(
					'role_id'	=> $resourceId,
					'aco_id'	=> $resourceId,
					'permissions'	=> json_encode($operations)
				));
				$acl->save();
			}
		}
		else
		{
			$permissions = json_decode($acl->permissions,True);
		
			if( $allow === True ){
				$new_permissions = array_merge($permissions,$operations);
			}else{				
				foreach ($permissions as $key => $value) {
					if( in_array($value, $operations) ){
						print_r($value.' : removed <br>');
						unset($permissions[$key]);
					}
				}
				$new_permissions = $permissions;
			}
			
			dd($new_permissions);
			$acl->permissions = json_encode($new_permissions);
			$acl->save();
		}
	}
}