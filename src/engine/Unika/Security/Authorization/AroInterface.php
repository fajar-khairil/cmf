<?php
/**
 *	This file is part of the Unika-CMF project.
 *	ARO(Access Request Object) Interface
 *	
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Security\Authorization;

interface Aro
{
	/**
	 *
	 *	the name of this aro node
	 *
	 *	@return string
	 */
	public function getAroName();

	/**
	 *
	 *	the unique identifier of this aro node
	 *
	 *	@return mixed
	 */		
	public function getAroIdentifier();

	/**
	 *
	 *	return description of this aro
	 */
	public function getAroDescription();
}