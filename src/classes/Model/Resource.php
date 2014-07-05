<?php
/**
 *	This file is part of the Unika-CMF project.
 *	Authorization\Resource Eloquent Implementation
 *	
 *	@license MIT
 *	@author Fajar Khairil
 */

use Illuminate\Database\Eloquent\Model as Eloquent;
use Unika\Security\Authorization\ResourceInterface;

class Resource extends Eloquent implements ResourceInterface
{
	protected $table = 'aros';
	protected $guarded = array('id');
	protected $fillable = array('name','description','permissions');

	public function __construct(array $attributes = array())
	{
		parent::__construct($attributes);
		$app = \Application::instance();
		$this->table = $app['config']['acl.eloquent.resource_table'];
	}

	public function getResourceId()
	{

		return $this->getKey();
	}

	public function getResourceName()
	{
		return $this->name;
	}

	public function getResourceDescription()
	{
		return $this->description;
	}

	//return array of permissions
	public function getPermissions()
	{
		return json_decode($this->permissions,True);
	}
}