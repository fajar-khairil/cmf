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
use Unika\Security\Authorization\AclException;

class ResourceRegistry implements ResourceRegistryInterface
{
	protected $app;
	protected $resource_table;
	protected $Table;

	public function __construct(Application $app)
	{
		$this->app = $app;
		$this->resource_table = $this->app->config('acl.Database.resource_table');
		$this->Table = $this->app['database']->table($this->resource_table);
	}

	protected function getTable()
	{
		return ORM::for_table($this->resource_table);
	}


	public function addResource(Array $resource)
	{
		$res = isset($resource['id']) ? $resource['id'] : preg_replace('/[.," "]/', '_',$resource['name']);
		
		$Resource = $this->getResource($res);

		if( !$Resource )
		{
			return $this->Table->insert(
				[
					'created_at' => date('Y-m-d H:i:s'),
					'name'		=> $Resource['name'],
				]
			);
		}
		else
		{
			return $this->Table->update(
				[
					'updated_at' => date('Y-m-d H:i:s'),
					'name'		=> $Resource['name'],
				]
			);
		}	
	}

	public function removeResource($resource)
	{
		if( is_numeric($resource) )
		{
			return $this->Table->find($resource)->delete();	
		}
		elseif( is_string( $resource ) )
		{
			return $this->Table->where(['name' => $resource])->delete();		
		}
		else
		{
			$errmsg = $resource.' invalid resource given in '.__FILE__.' : '.__FUNCTION__.' ['.__LINE__.']'.PHP_EOL.$_SERVER['REMOTE_ADDR'];
			$this->app['logger']->addCritical($errmsg);
			throw new AclException($errmsg);						
		}
	}

	public function hasResource($resource)
	{
		return (boolean)$this->getResource($resource);				
	}

	public function allResource()
	{
		return $this->Table->all();
	}

	public function getResource($resource)
	{
		if( is_numeric($resource) )
			return $this->Table->find($resource);	
		else
			return $this->Table->where(['name' => $resource])->first();				
	}
}