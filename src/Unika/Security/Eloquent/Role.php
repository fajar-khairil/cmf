<?php
/**
 *	This file is part of the Unika-CMF project.
 *	Role using Illuminate\Database\Eloquent\Model
 *	
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Security\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Unika\Security\Authorization\AroInterface;

class Role extends Model implements AroInterface
{
	protected $table = 'roles';

	public function users()
	{
		$this->hasMany('User');
	}

	/**
	 *
	 *	the name of this aro node
	 *
	 *	@return string
	 */
	public function getAroName()
	{
		return $this->name;
	}

	/**
	 *
	 *	the unique identifier of this aro node
	 *
	 *	@return mixed
	 */		
	public function getAroIdentifier()
	{
		return $this->id;
	}

	/**
	 *
	 *	return description of this aro
	 */
	public function getAroDescription()
	{
		return $this->description;
	}	
}