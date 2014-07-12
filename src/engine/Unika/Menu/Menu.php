<?php
/**
 *	This file is part of the Unika-CMF project.
 *	Menu System
 *
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Menu;

use Illuminate\Support\Collection;
use Unika\Menu\Eloquent\Menu as Node;

class Menu
{

	//driver instance
	protected $driver;

	public function __construct($driver)
	{
		
	}

	/**
	 *	
	 *	@param string $group name of the menu group
	 *	@return Collection of Menu Node
	 */
	public function tree($group)
	{
		if( !is_string($group) ) throw new \RuntimeException('invalid supplied argument.');


	}

	/**
	 *
	 *	store a collection of menu hirarchy
	 *	
	 */
	public function store(Collection $collection)
	{

	}

	/**
	 *
	 *	put a node of menu to system
	 *	
	 *	@param Node
	 *	@param mixed nodeId | Node specify parent of supplied node
	 *	@return boolean true on success false on failed
	 */
	public function put($node,$parentNode = null)
	{

	}

	/**
	 *	
	 *	get Node of Menu by identifier
	 *
	 *	@param mixed $nodeId
	 *	@return Node Menu
	 */
	public function get($nodeId)
	{

	}

	/**
	 *	
	 *
	 *	@param mixed nodeId to remove
	 */
	public function remove($nodeId)
	{

	}

	/**
	 *
	 *	remove all menus
	 *	@param string $group name of menu group
	 */
	public function removeAll($group)
	{

	}
}