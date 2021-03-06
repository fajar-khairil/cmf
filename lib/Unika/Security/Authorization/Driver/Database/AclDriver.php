<?php
/**
 *	This file is part of the UnikaCMF project.
 *	
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Security\Authorization\Driver\Database;

use Unika\Security\Authorization\AclDriverInterface;
use Unika\Application;

class AclDriver implements AclDriverInterface
{
	protected $acl_table;
	protected $app;

	public function __construct(Application $app)
	{
		$this->app = $app;
		$this->acl_table = $this->app->config('acl.drivers.Database.acl_table');
		$this->Table = $this->app['database']->table($this->acl_table);
	}

	/**
	 *	@param string roleName
	 *	@param string resourceName
	 *	@param string operation name
	 *	@return boolean
	 */
	public function queryAcl($role,$resource,$operation = null)
	{	
		$acl = $this->Table->where(
			[
				'role_id'	=> $role,
				'aco_id'	=> $resource
			]
		)->first();

		$permissions = json_decode($acl['permissions'],True);

		if( !is_array($permissions) ) return False;

		$permission = isset($permissions[0]) ? $permissions[0] : null;
		if(  $permission == "*" ){ return True; }

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
		$values = array(
			'role_id'	=> $roleId,
			'aco_id'	=> $resourceId
		);
		$acl = $this->Table->where($values)->first();

		if( !$acl )
		{
			if( $allow === True )
			{
				// new fresh acl rule
				return $this->Table->insert(array(
					'role_id'	=> $roleId,
					'aco_id'	=> $resourceId,
					'permissions'	=> json_encode($operations),
					'created_at'	=> date('Y-m-d H:i:s')
				));
			}
		}
		else
		{
			$permissions = json_decode($acl['permissions'],True);

			if( $allow === True )
			{
				if( is_array($permissions) )
				{
					$new_permissions = array_unique(array_merge($permissions,$operations));
				}
				else
				{
					$new_permissions = $operations;
				}
			}
			else
			{			
				$new_permissions = array();	
				foreach ($permissions as $key => $value) 
				{
					if( in_array($value, $operations) ){
						unset($permissions[$key]);
					}else{
						$new_permissions[] = $value;
					}
				}

				sort($permissions,SORT_NUMERIC);
				$new_permissions = $permissions;				
			}

			$sql = 'update '.$this->acl_table.' SET permissions = ? , updated_at = NOW() where role_id = ? AND aco_id = ?';
			$this->Table->getConnection()->update($sql,[ json_encode($new_permissions), $roleId,$resourceId ]);
		}
	}

	public static function createTablesDependencies(\Unika\Application $app ,\Illuminate\Database\Schema\Builder $schema)
	{
		$schema->create($app->config('acl.drivers.Database.role_table'),function($blueprint)
		{
		  $blueprint->integer('id',True,True);
		  $blueprint->string('name');
		  $blueprint->string('description');

		  $blueprint->softDeletes();
		  $blueprint->nullableTimestamps();
		});		

		$schema->create($app->config('acl.drivers.Database.resource_table'),function($blueprint)
		{
		  $blueprint->integer('id',True,True);
		  $blueprint->string('name');

		  $blueprint->softDeletes();
		  $blueprint->nullableTimestamps();
		});		

		$schema->create($app->config('acl.drivers.Database.acl_table'),function($blueprint)
		{
		  $blueprint->integer('id',True,True);
		  $blueprint->integer('role_id');
		  $blueprint->integer('aco_id');
		  $blueprint->text('permissions');

		  $blueprint->softDeletes();
		  $blueprint->nullableTimestamps();
		});				
	}
}