<?php
/**
 *	This file is part of the Unika-CMF project.
 *	Authorization\Role Eloquent Implementation
 *	
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Security\Authorization\Eloquent;

use Unika\Security\Authorization\ResourceRegistryInterface;
use Unika\Security\Authorization\ResourceInterface;

class ResourceRegistry implements ResourceRegistryInterface
{
	protected $app;
	protected $resource_class;
	protected $resource_table;

	public function __construct(\Application $app)
	{
		$this->app = $app;
		$this->resource_table = $this->app['config']['acl.eloquent.resource_table'];
		$this->resource_class = $this->app['config']['acl.eloquent.resource_implementation'];
	}

	//return ResourceInterface
	public function createResource($name)
	{
		$res = $this->get(preg_replace('/[.," "]/', '_', $name));
		if( $res ) return $res;

		return new $this->resource_class(['name' => $name]);
	}

	public function add(ResourceInterface $resource)
	{
		$values = array(	
			'name'	=>	$resource->getResourceName()
		);

		$res = new $this->resource_class;
		$res->id = $resource->getResourceId();
		if( $res->id !== NULL )
			$res->exists = True;
		
		$res->fill($values);
		if( $res->save() )
			return $res;
		else
			return False;		
	}

	public function remove($resource)
	{
		if( $resource instanceof ResourceInterface ){
			$resource = $resource->getResourceId();
		}

		if( !$resource ) return;

		$node = new $this->resource_class;		
		return $node->delete();
	}

	public function removeAll()
	{
		$capsule = $this->app['capsule'];
		return $capsule::table($this->resource_table)->delete();
	}

	public function has($resource)
	{
		return (boolean)$this->get($resource);
	}

	public function all()
	{
		$node = new $this->resource_class;
		return $node->all();
	}

	public function get($resource)
	{
		if( $resource instanceof ResourceInterface )
			$resource = $resource->getResourceId();

		$capsule = $this->app['capsule'];

		$query = $capsule::table($this->resource_table);		

		if( is_string($resource) )
		{
			$query->where('name',$resource);
		}
		else
		{
			$query->where('id',$resource);
		}

		$row = $query->take(1)->get();
		unset($capsule);

		if( !empty($row) )
		{
			$obj = new $this->resource_class;
			$obj->id = $row[0]['id'];
			$obj->name = $row[0]['name'];
			$obj->exists = True;
			return $obj;
		}

		return NULL;
	}
}