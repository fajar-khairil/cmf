<?php
/**
 *	This file is part of the Unika-CMF project.
 *	default User Implementation
 *	
 *	@license MIT
 *	@author Fajar Khairil
 *
 */

use Illuminate\Database\Eloquent\Model;
use Unika\Security\Authentication\AuthUserInterface;
use Unika\Security\Authentication\AuthRememberUserInterface;
use Unika\Security\Authorization\RoleInterface;

class Model_User extends Model implements AuthUserInterface,AuthRememberUserInterface,RoleInterface
{
	protected $app;
	protected $hidden = array('pass','salt');
	protected $guarded = array('pass','salt');

	public function __construct(array $attributes = array())
	{
		parent::__construct($attributes);
		$this->app = \Application::instance();
		$this->table = $this->app['config']['auth.Eloquent.user_table'];
		$this->guarded[] = $this->app['config']['auth.remember_token_column'];
	}

	//role relation
    public function role()
    {
        return $this->belongsTo($this->app['config']['acl.eloquent.role_class']);
    }	

    //RoleInterface
	public function getRoleId()
	{
		return $this->role->getKey();
	}

	public function getRoleName()
	{
		return $this->role->name;		
	}

	public function getRoleDescription()
	{
		return $this->role->description;
	}	

	//AuthUserInterface
	public function getAuthIdentifier()
	{
		return $this->id;
	}

	public function getPassword()
	{
		return $this->pass;
	}

	public function getSalt()
	{
		return $this->salt();
	}	

	//AuthRemembermeInterface
	public function getRememberMeToken()
	{
		return $this->{$this->app['config']['auth.remember_token_column']};
	}
}