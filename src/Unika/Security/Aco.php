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

use Unika\Securit\Authorization\AcoInterface;
use Unika\Securit\Authorization\PermissionInterface;
use Unika\Securit\Authorization\AuthorizationException;
use Illuminate\Database\Eloquent\Model;

class Aco extends Model implements AcoInterface
{

	protected $table = 'acos';

	/**
	 *
	 *	get all permission of this aco
	 */
	public function permissions()
	{
		return $this->hasMany('Permission');
	}

	/**
	 *
	 *	@param $permissions PermissionInterface or array of Permission interface
	 *	throw AuthorizationException if supplied parameters not valid Permission
	 */
	public function addPermission(PermissionInterface $permission)
	{
		if( !is_numeric( $permission->getIdentifier() ) )
			throw new AuthorizationException('wrong permission attached');

		$this->permissions()->save($permission);
	}

	/**
	 *
	 *	adding array of permissions
	 */
	public function addPermissions(array $permissions)
	{
		throw new AuthorizationException('not yet implemented');
	}

	/**
	 *
	 *	return name of this Aco
	 */
	public function getAcoName()
	{
		return $this->name;
	}

	/**
	 *
	 *	return unique identifier of this Aco
	 */
	public function getAcoIdentifier()
	{
		return $this->id;
	}

	/**
	 *
	 *	return description of this Aco
	 */
	public function getAcoDescription()
	{
		return $this->description;
	}
}