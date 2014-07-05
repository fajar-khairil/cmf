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


class Role extends Eloquent
{
	protected $fillable = array('name','description');
	protected $app;

	public function __construct(array $attributes = array())
	{
		parent::__construct($attributes);
		$this->app = \Application::instance();
		$this->table = $this->app['config']['acl.eloquent.role_table'];
	}

	//belongsTo relation
	public function users()
	{
		return $this->hasMany($this->app['config']['auth.Eloquent.user_class']);
	}
}