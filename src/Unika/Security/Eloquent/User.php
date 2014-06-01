<?php
/**
 *	Unika-CMF Project
 *	User class implementing important authentication and authorization interface
 *	using Illuminate\Database\Eloquent\Model
 *	
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Security\Eloquent;

use Unika\Security\Authentication\AuthUserInterface;
use Unika\Security\Authentication\AuthRememberUserInterface;
use Illuminate\Database\Eloquent\Model;

class User extends Model implements 
	AuthUserInterface,
	AuthRememberUserInterface
{

	protected $table = 'users';

	public function role()
	{
		$this->belongsTo('Role');
	}

	//it can be username/email or whatever
	public function getAuthIdentifier()
	{
		return $this->username;
	}

	public function getPassword()
	{
		return $this->pass;
	}

	public function getSalt()
	{
		return $this->salt;
	}

	public function getRememberMeToken()
	{
		return $this->remember_token;
	}	
}