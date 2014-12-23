<?php
/**
 *	This file is part of the UnikaCMF project.
 *	
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Security\Authorization\Driver\Database;

use Unika\Security\Authorization\RoleRegistryInterface;
use Unika\Application;
use Unika\Security\Authorization\AccessDeniedHttpException;

class RoleRegistry implements RoleRegistryInterface
{
	protected $app;
	protected $role_table;
	protected $Table;

	public function __construct(Application $app)
	{
		$this->app = $app;
		$this->role_table = $this->app->config('acl.Database.role_table');
		$this->Table = $this->app['database']->table($this->role_table);
	}

	public function addRole(array $role)
	{
		$roleID = isset($role['id']) ? $role['id'] : preg_replace('/[.," "]/', '_',$role['name']);

		$Role = $this->getRole( $roleID );unset($roleID);
		
		if( !$Role )
		{
			return $this->Table->insert(
				[
					'created_at' => date('Y-m-d H:i:s'),
					'name'		=> $role['name'],
					'description' => $role['description']
				]
			);
		}
		else
		{
			return $this->Table->update(
				[
					'updated_at' => date('Y-m-d H:i:s'),
					'name'		=> $role['name'],
					'description' => $role['description']
				]
			);
		}
	}

	public function removeRole($role)
	{
		if( is_numeric($role) )
		{
			return $this->Table->find($role)->delete();	
		}
		elseif( is_string( $role ) )
		{
			return $this->Table->where(['name' => $role])->delete();		
		}
		else
		{
			$errmsg = $role.' invalid resource given in '.__FILE__.' : '.__FUNCTION__.' ['.__LINE__.']'.PHP_EOL.$_SERVER['REMOTE_ADDR'];
			$this->app['logger']->addCritical($errmsg);
			throw new AccessDeniedHttpException($errmsg);						
		}
	}

	public function hasRole($role)
	{
		return (boolean)$this->getRole();
	}

	public function allRole()
	{
		return $this->Table->all();
	}

	public function getRole($role)
	{
		if( is_numeric($role) )
		{
			return $this->Table->find($role);
		}
		elseif( is_string( $role ) )
		{
			return $this->Table->where(['name' => $role])->first();
		}
		else
		{
			$errmsg = $role.' invalid role given in '.__FILE__.' : '.__FUNCTION__.' ['.__LINE__.']'.PHP_EOL.$_SERVER['REMOTE_ADDR'];
			$this->app['logger']->addCritical($errmsg);
			throw new AccessDeniedHttpException($errmsg);
		}
		
	}
}