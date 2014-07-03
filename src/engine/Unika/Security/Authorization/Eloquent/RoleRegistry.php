<?php
/**
 *	This file is part of the Unika-CMF project.
 *	Authorization\Role Eloquent Implementation
 *	
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Security\Authorization\Eloquent;

use Unika\Security\Authorization\RoleRegistryInterface;
use Unika\Security\Authorization\RoleInterface;

class RoleRegistry implements RoleRegistryInterface
{
	protected $app;
	protected $role_table;

	public function __construct(\Application $app)
	{
		$this->app = $app;
		$this->role_table = $this->app['config']['acl.eloquent.role_table'];
	}

	public function add(RoleInterface $role)
	{	
		$exists = False;
		if( $role->getRoleId() )
			$exists = $this->has($role->getRoleId());
	
		$capsule = $this->app['capsule'];

		$values = array(
			'name'			=>	$role->getRoleName(),
			'description'	=>	$role->getRoleDescription()
		);

		if( !$exists )
		{
			$values['created_at'] = date('Y-m-d H:i:s',time());
			return $capsule::table($this->role_table)
			->insert($values);
		}
		else
		{
			$values['updated_at'] = date('Y-m-d H:i:s',time());
			return $capsule::table($this->role_table)
			->update($values);
		}
	}

	public function remove($roleId)
	{
		$capsule = $this->app['capsule'];
		return $capsule::table($this->role_table)
		->where('id',$roleId)
		->delete();		
	}

	public function removeAll()
	{
		$capsule = $this->app['capsule'];
		return $capsule::table($this->role_table)
		->delete();				
	}

	public function has($roleId)
	{
		return (boolean)$this->get($roleId);		
	}

	public function all()
	{
		$capsule = $this->app['capsule'];
		$records = $capsule::table($this->role_table)
				   ->get();
		
		$results = new \Illuminate\Database\Eloquent\Collection;
		$role_class = $this->app['config']['acl.eloquent.role_class'];
		foreach( $records as $row )
		{
			$obj = new $role_class;
			$obj->id = $row['id'];
			$obj->name = $row['name'];
			$obj->description = $row['description'];
			$obj->exists = True;

			$results->push($obj);
		}

		return $results;
	}

	public function get($roleId)
	{
		if( $roleId instanceof RoleInterface )
			$roleId = $roleId->getRoleId();
		
		$capsule = $this->app['capsule'];

		$row = $capsule::table($this->role_table)
		->where('id',$roleId)
		->take(1)->get();
		unset($capsule);

		if( !empty($row) )
		{
			$obj = new $this->app['config']['acl.eloquent.role_class'];
			$obj->id = $row[0]['id'];
			$obj->name = $row[0]['name'];
			$obj->description = $row[0]['description'];
			$obj->exists = True;
			return $obj;
		}

		return NULL;
	}
}