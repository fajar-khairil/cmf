<?php
/**
 *	This file is part of the Unika-CMF project.
 *	Authorization\Role Eloquent Implementation
 *	
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Security\Authorization\Eloquent;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Unika\Security\Authorization\RoleInterface;

class Role extends Eloquent implements RoleInterface
{
	protected $fillable = array('name','description');
	
	public function __construct(array $attributes = array())
	{
		parent::__construct($attributes);
		$app = \Application::instance();
		$this->table = $app['config']['acl.eloquent.role_table'];
	}

	public function getRoleId()
	{
		return $this->getKey();
	}

	public function getRoleName()
	{
		return $this->name;		
	}

	public function getRoleDescription()
	{
		return $this->description;
	}	
}