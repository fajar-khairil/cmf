<?php
/**
 *	Unika-CMF Project
 *	default Auth Service Implementation
 *	
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Security\Auth;

class AuthGenericUser extends \Illuminate\Support\Fluent implements AuthUserInterface,AuthRememberUserInterface
{

	/**
	 * Create a new fluent container instance.
	 *
	 * @param  array  $attributes
	 * @return void
	 */
	public function __construct($attributes = array())
	{
		foreach ($attributes as $key => $value)
		{
			$this->attributes[$key] = $value;
		}

		$this->attributes = array_merge(
			array(
				'id' => null,
				'password' => null,
				'salt'	=> null,
				'rememberme_token_name' => 'remember_token',
				'remember_token'	=> null
			),
			$this->attributes
		);
	}

	//it can be username/email or whatever
	public function getIdentifier()
	{
		return $this->id;
	}

	public function getPassword()
	{
		return $this->password
	}

	public function getSalt()
	{
		return $this->salt;
	}

	public function getRememberMeToken()
	{
		return $this->remember_token;
	}

	public function getRememberMeTokenName()
	{
		return $this->rememberme_token_name;
	}	
}