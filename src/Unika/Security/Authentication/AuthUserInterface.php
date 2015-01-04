<?php
/**
 *	This file is part of the UnikaCMF project
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */


interface AuthUserInterface
{
	/**
	 *	@return string
	 */
	public function getUsername();

	/**
	 *	@return string
	 */
	public function getPassword();

	/**
	 *	@return string
	 */
	public function getSalt();
}