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
use Unika\Menu\NodeInterface;

class Menu
{

	//driver instance
	protected $driver;
	protected $renderer;

	public function __construct($driver,$renderer)
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

		return $this->driver->buildTree($group);
	}

	/**
	 *
	 *	store a collection of menu hierarchy
	 *	
	 *	@param string $group name of group menu
	 *	@param Collection $collection of menu to save
	 */
	public function store($group,Collection $collection)
	{
		$this->driver->storeCollection($group,$collection)
	}

	/**
	 *
	 *	put a node of menu to system
	 *	
	 *	@param string $group name of group menu
	 *	@param mixed nodeId | Node specify parent of supplied node
	 *	@return boolean true on success false on failed
	 */
	public function put($group,NodeInterface $node)
	{
		$this->driver->put($group,$node);
	}

	/**
	 *
	 *	remove all menus
	 *	@param string $group name of menu group
	 */
	public function removeAll($group)
	{
		$this->driver->destroy($group);
	}

	public function render($group)
	{
		return $this->renderer->render($this->tree($group));
	}
}