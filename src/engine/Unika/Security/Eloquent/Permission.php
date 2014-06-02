<?php
/**
 *	This file is part of the Unika-CMF project.
 *
 *	Aco Permission implementation using Illuminate\Database\Eloquent\Model
 *	
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Security\Eloquent;

use Unika\Securit\Authorization\PermissionInterface;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model implements PermissionInterface
{

	protected $table = 'permissions';

	/**
	 *
	 *	get all permission of this aco
	 */
	public function aco()
	{
		return $this->belongsTo('Aco');
	}

	/**
	 *
	 *	return name of this Aco
	 */
	public function getPermissionName()
	{
		return $this->name;
	}

	/**
	 *
	 *	return unique identifier of this Aco
	 */
	public function getPermissionIdentifier()
	{
		return $this->id;
	}

	/**
	 *
	 *	return description of this Aco
	 */
	public function getPermissionDescription()
	{
		return $this->description;
	}
}