<?php
/**
 *	This file is part of the Unika-CMF project.
 *	
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Security\Authorization\Driver\Database;

use Unika\Security\Authorization\RoleRegistryInterface;
use Unika\Application;
use ORM;

class RoleRegistry implements RoleRegistryInterface
{
	protected $app;
	protected $role_table;

	public function __construct(Application $app)
	{
		$this->app = $app;
		$this->role_table = $this->app->config('acl.Database.role_table');
	}

	protected function getTable()
	{
		return ORM::for_table($this->role_table);
	}

	public function addRole(array $role)
	{
		$Role = $this->getTable()->find_one($role['id']);
		
		if( !$Role )
		{
			$Role = $this->getTable()->create();
			$Role->created_at = date('Y-m-d H:i:s');
		}
		else
		{
			$Role->updated_at = date('Y-m-d H:i:s');
		}

		$Role->name = $role['name'];
		$Role->description = $role['description'];		

		return $Role->save();
	}

	public function createRole(array $attributes)
	{
		if( isset($attributes['id']) ){
			$res = $this->getRole($attributes['id']);
			if( $res )
				return $res;

			unset($attributes['id']);
		}

		if( isset($attributes['name']) ){
			$attributes['name'] = preg_replace('/[.," "]/', '_', $attributes['name']);
			$res = $this->get($attributes['name']);
			if( $res )
				return $res;
		}

		$attributes['created_at'] = date('Y-m-d H:i:s');
		return $this->getTable()->create($attributes);
	}

	public function removeRole($roleId)
	{
		return $this->getTable()->find_one($roleId)->delete();
	}

	public function hasRole($roleId)
	{
		return (boolean)$this->getTable()->find_one();
	}

	public function allRole()
	{
		return $this->getTable()->find_many();
	}

	public function getRole($roleId)
	{
		return $this->getTable()->find_one($roleId);
	}
}