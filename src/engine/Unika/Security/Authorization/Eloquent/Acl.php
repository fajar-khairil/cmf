<?php
/**
 *	This file is part of the Unika-CMF project.
 *	Acl Eloquent Driver Implementation
 *
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Security\Authorization\Eloquent;

use Unika\Security\Authorization\AclDriverInterface;
use Unika\Security\Authorization\ResourceInterface;

class Acl implements AclDriverInterface
{
	protected $app;
	protected $cache;

	public function __construct(\Application $app)
	{
		$this->app = $app;
		$this->cache = $this->app['cache'];
	}

	protected function getAcl($roleId,$resourceId)
	{	
		$app = $this->app;
		$row = $this->cache->rememberForever('acl_'.$roleId.'_'.$resourceId,function() use($app,$roleId,$resourceId)
		{
			$capsule = $app['capsule'];
			return $capsule::table($app['config']['acl.eloquent.acl_table'])
			->where('role_id',$roleId)
			->where('aco_id',$resourceId)
			->take(1)
			->get();

		});

		return $row;
	}

	protected function getResourceWildcardId()
	{
		$wildcardId = $this->cache->get('WILDCARD_ID');

		if( !$wildcardId )
		{
			$capsule = $this->app['capsule'];
			$row = $capsule::table($this->app['config']['acl.eloquent.resource_table'])
			->where('name','*')
			->take(1)->get();
			unset($capsule);
			
			if( empty($row) ) return False;	
		}

		$wildcardId = $row[0]['id'];
		$this->cache->put('WILDCARD_ID',$wildcardId);
		
		return $wildcardId;			
	}

	protected function isRoleHaveWildCard($roleId)
	{
		$resourceId = $this->getResourceWildcardId();

		if( !$resourceId ) return False;
		$row = $this->getAcl($roleId,$resourceId);
		return (boolean) $row;		
	}


	/**
	 *
	 *	@param integer roleId
	 *	@param integer aroId
	 *	@param string operation name
	 *	@return boolean
	 */
	public function queryAcl($roleId,$resource,$operation = null)
	{	
		if( $resource === NULL ){
			return $this->isRoleHaveWildCard($roleId);
		}elseif( $resource instanceof ResourceInterface ){
			$resourceId = $resource->getResourceId();
		}else{
			return False;
		}

		$row = $this->getAcl($roleId,$resourceId);

		if( !empty($row) )
		{
			$permissions = json_decode($row[0]['permissions'],True);
			
			//return true if permissions is wildcard
			if( in_array('*', $permissions) ) return True;

			

			if( is_array($operation) )
			{
				$flag = True;
				foreach( $operation as $op )
				{
					if( !in_array($op, $permissions) ){
						$flag = False;
						break;
					}
				}

				if( ! $flag ){
					return $this->isRoleHaveWildCard($roleId);
				}		

				return $flag;		
			}
			else
			{
				$flag = in_array($operation, $permissions);
				if( !$flag )
					return $this->isRoleHaveWildCard($roleId);

				return $flag;
			}					
		}

		return False;
	}

	/**
	 *	@param integer roleId
	 *	@param integer resourceId
	 *	@param array operations array of operations
	 *	@param boolean should allow supplied role to given resource and operation?
	 */
	public function setRules($roleId,$resourceId,array $operations = array('*'),$allow)
	{
		$this->cache->forget('acl_'.$roleId.'_'.$resourceId);
		
		$row = $this->getAcl($roleId,$resourceId);
		if( !empty($row) AND (boolean)$allow === False )
		{
			$capsule = $this->app['capsule'];
			return $capsule::table($this->app['config']['acl.eloquent.acl_table'])
			->where('role_id',$roleId)
			->where('aco_id',$resourceId)
			->delete();
		}
		elseif( empty($row) AND (boolean)$allow === True )
		{
			$capsule = $this->app['capsule'];
			return $capsule::table($this->app['config']['acl.eloquent.acl_table'])
			->insert( ['role_id' => $roleId,'aco_id' => $resourceId,'permissions' => json_encode($operations) ]);			
		}
		elseif( !empty($row) AND (boolean)$allow === True )
		{
			$capsule = $this->app['capsule'];
			return $capsule::table($this->app['config']['acl.eloquent.acl_table'])
			->where('id',$row[0]['id'])
			->update( ['permissions' => json_encode($operations) ]);
		}
	}
}