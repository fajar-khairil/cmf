<?php
/**
 *	This file is part of the Unika-CMF project.
 *	
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Security\Authorization\Driver\Database;

use Unika\Security\Authorization\ResourceRegistryInterface;
use Unika\Application;
use ORM;

class ResourceRegistry implements ResourceRegistryInterface
{
	protected $app;
	protected $resource_table;

	public function __construct(Application $app)
	{
		$this->app = $app;
		$this->resource_table = $this->app->config('acl.Database.resource_table');
	}

	protected function getTable()
	{
		return ORM::for_table($this->resource_table);
	}


	public function addResource(Array $resource)
	{
		$res = isset($resource['id']) ? $resource['id'] : $resource['name'];
		$Role = $this->getResource($res);
		
		if( !$Role )
		{
			$Role = $this->getTable()->create();
			$Role->created_at = date('Y-m-d H:i:s');
		}
		else
		{
			$Role->updated_at = date('Y-m-d H:i:s');
		}

		$Role->name = $resource['name'];

		return $Role->save();		
	}

	public function createResource($name)
	{
		$name = preg_replace('/[.," "]/', '_', $name);
		$res = $this->getTable()->where(['name' => $name])->find_one();
		if( $res )
			return $res;

		return $this->getTable()->create(array(
			'name'	=> $name,
			'created_at' => date('Y-m-d H:i:s')
		));
	}

	public function removeResource($resource)
	{
		if( is_numeric($resource) )
			return $this->getTable()->find_one($resource)->delete();	
		else
			return $this->getTable()->where(['name' => $resource])->delete();		
	}

	public function hasResource($resource)
	{
		if( is_numeric($resource) )
			return (boolean)$this->getTable()->find_one($resource);	
		else
			return (boolean)$this->getTable()->where(['name' => $resource])->find_one();			
	}

	public function allResource()
	{
		return $this->getTable()->find_many();
	}

	public function getResource($resource)
	{
		if( is_numeric($resource) )
			return $this->getTable()->find_one($resource);	
		else
			return $this->getTable()->where(['name' => $resource])->find_one();				
	}
}