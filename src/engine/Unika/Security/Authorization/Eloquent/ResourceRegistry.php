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
		$this->resource_class = $this->app['config']['acl.eloquent.resource_class'];
	}

	public function add(ResourceInterface $resource,ResourceInterface $parent = null)
	{
		$resourceExists = False;
		if( $resource->getResourceId() )
			$resourceExists = $this->has($resource->getResourceId());


		$values = array(
			'name'			=>	$resource->getResourceName(),
			'description'	=>	$resource->getResourceDescription()
		);

		$parentExists = False;

		if( $parent !== null ){
			$parent = $this->get($parent->getResourceId());
			if( $parent ){
				$parentExists = True;
			}
		}

		if( !$resourceExists )
		{
			$values['created_at'] = date('Y-m-d H:i:s',time());
			$res = new $this->resource_class;
			$res->fill($values);
			if( $parentExists === True ){
				$res->parent_id = $parent->getResourceId();
				return $res->save();
			}else{
				return $res->saveAsRoot();
			}
		}
		else
		{
			$values['updated_at'] = date('Y-m-d H:i:s',time());
			if( $parentExists === True )
			{
				$res = new $this->resource_class;
				$res->fill($values);
				$res->parent_id = $parent->getResourceId();
				return $res->save();
			}
			else
			{
				$capsule = $this->app['capsule'];

				return $capsule::table($this->resource_table)
				->update($values);
			}
		}
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

	public function isInherit($childResource,$parentResource)
	{   
		if( !$childResource OR !$parentResource ) throw new \RuntimeException('Invalid Resource supplied.');

		if( !$childResource instanceof \Kalnoy\Nestedset\Node ){
			$childResource = $this->get($childResource);
		}

		if( !$parentResource instanceof \Kalnoy\Nestedset\Node ){
			$parentResource = $this->get($parentResource);
		}

		return (boolean)$childResource->isDescendantOf($parentResource);
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
		$row = $capsule::table($this->app['config']['acl.eloquent.resource_table'])
			   ->where('id',$resource)
			   ->take(1)
			   ->get();

		unset($capsule);

		if( !empty($row) )
		{
			$obj = new $this->resource_class;
			$obj->id = $row[0]['id'];
			$obj->name = $row[0]['name'];
			$obj->description = $row[0]['description'];
			$obj->exists = True;
			return $obj;
		}

		return NULL;
	}

	public function getParent($resource)
	{
		if( !$resource ) throw new \RuntimeException('Invalid Resource Supplied.');

		if( !$resource instanceof \Kalnoy\Nestedset\Node ){
			$resource = $this->get($resource);
		}

		$row = $resource->hasParent()->take(1)->get();

		return $row[0];
	}
}