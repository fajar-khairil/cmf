<?php
/**
 *	This file is part of the Unika-CMF project.
 *	Permission Interface
 *	
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Security\Authorization;

interface PermissionInterface 
{

	/**
	 *
	 *	get Name of this permission
	 */
	public function getPermissionName();

	/**
	 *
	 *	get description of this permission
	 */
	public function getPermissionDescription();

	/**
	 *
	 *	get unique identifier of this permission
	 */
	public function getPermissionIdentifier();
}