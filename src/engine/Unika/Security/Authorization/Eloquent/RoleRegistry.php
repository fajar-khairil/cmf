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

	//return RoleInterface
	public function createRole(array $attributes = array())
	{
		if( isset($attributes['id']) ){
			$res = $this->get($attributes['id']);
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

		$role_class = $this->app['config']['acl.eloquent.role_class'];
		return new $role_class($attributes);		
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

		$role_class = $this->app['config']['acl.eloquent.role_class'];
		$instance = new $role_class;
		$instance->id = $resource->getResourceId();
		if( $instance->id !== NULL )
			$instance->exists = True;
		
		$instance->fill($values);
		if( $instance->save() )
			return $instance;
		else
			return False;
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
		$role_class = $this->app['config']['acl.eloquent.role_implementation'];
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

		$query = $capsule::table($this->role_table);		

		if( is_numeric($roleId) )
		{
			$query->where('id',$roleId);
		}
		else
		{
			$query->where('name',$roleId);
		}

		$row = $query->take(1)->get();
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