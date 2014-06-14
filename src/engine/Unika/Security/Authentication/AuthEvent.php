<?php
/**
 *	This file is part of the Unika-CMF project.
 *	AuthEvent
 *	
 *	@license MIT
 *	@author Fajar Khairil
 *
 */

namespace Unika\Security\Authentication;

use Unika\Security\Authentication\AuthInterface;

class AuthEvent extends \Symfony\Component\EventDispatcher\Event
{
	protected $resource;

	/**
	 *
	 *
	 *	it can be anything auth want to pass
	 */
	public function __construct($resource)
	{
		$this->resource = $resource;
	}

	public function getResource()
	{
		return $this->resource;
	}
}