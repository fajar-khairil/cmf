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
	protected $group = null;

	public function __construct(DriverInterface $driver,RendererInterface $renderer)
	{
		$this->renderer = $renderer;
		$this->driver = $driver;
	}

	/**
	 *	set group menu for this instance
	 *
	 *	@param string $group name of the menu group
	 */
	public function setGroup($group)
	{
		$instance = $this->driver->groupExists($group);
		
		if( False === $instance )
		{
			$this->driver->put( new \Unika\Menu\Node(['title' => $group]) );
		}
	}

	/**
	 *	
	 *	@param string $group name of the menu group
	 *	@return Collection of Menu Node
	 */
	public function tree()
	{
		return $this->driver->buildTree($this->group->id);
	}

	/**
	 *
	 *	put a node of menu to system
	 *	
	 *	@param string $group name of group menu
	 *	@param mixed nodeId | Node specify parent of supplied node
	 *	@return boolean true on success false on failed
	 */
	public function put(NodeInterface $node)
	{
		return $this->driver->put($node);
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
	public function removeAll()
	{
		return $this->driver->destroyAllChilds($group);
	}

	public function render($group)
	{
		
		return $this->renderer->render($this->tree());
	}

	function __call($method,$args)
	{
		if( $method != 'setGroup' ){		
			if( !$this->group ) throw new \Unika\Menu\Exception('you must set the group first.');
		}
	}
}