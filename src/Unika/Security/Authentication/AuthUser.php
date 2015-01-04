<?php
/**
 *	This file is part of the UnikaCMF project
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika\Security\Authentication;

class AuthUser implements AuthUserInterface
{
	protected $data;

	public function __construct(array $data)
	{
		$this->data = $data;
	}

	/**
	 *	@return string
	 */
	public function getUsername()
	{
		return $this->data['username'];
	}

	/**
	 *	@return string
	 */
	public function getPassword()
	{
		return $this->data['password'];
	}

	/**
	 *	@return string
	 */
	public function getSalt()
	{
		return $this->data['salt'];
	}
}