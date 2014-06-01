<?php
/**
 *	This file is part of the Unika-CMF project.
 *
 *	ACO(Access Control Object) implementation using Illuminate\Database\Eloquent\Model
 *	
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Security;

use Unika\Securit\Authorization\PermissionInterface;
use Unika\Securit\Authorization\AuthorizationException;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model implements AcoInterface
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