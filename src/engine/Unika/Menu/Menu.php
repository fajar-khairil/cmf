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

	public function __construct(DriverInterface $driver,RendererInterface $renderer)
	{
		$this->renderer = $renderer;
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
	 *	put a node of menu to system
	 *	
	 *	@param string $group name of group menu
	 *	@param mixed nodeId | Node specify parent of supplied node
	 *	@return boolean true on success false on failed
	 */
	public function put($group,NodeInterface $node)
	{
		return $this->driver->put($group,$node);
	}

	public function remove($node)
	{
		return $this->driver->remove($node);
	}

	/**
	 *
	 *	remove all menus
	 *	@param string $group name of menu group
	 */
	public function removeAll($group)
	{
		return $this->driver->destroy($group);
	}

	public function render($group)
	{
		return $this->renderer->render($this->tree($group));
	}
}