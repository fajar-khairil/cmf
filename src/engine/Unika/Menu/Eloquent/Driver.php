<?php
/**
 *	This file is part of the Unika-CMF project.
 *	Eloquent Menu  Driver Implementation
 *
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Menu\Eloquent;

class Driver implements Unika\Menu\DriverInterface
{
	public function buildTree($parentId)
	{

	}

	public function remove($node)
	{

	}

	public function destroyAllChilds($group)
	{

	}

	public function groupExists($group)
	{
		$menu = new \Unika\Menu\Eloquent\Menu(['title' => $group]);

		if( $menu->exists AND $menu->isRoot() )
		{
			return $menu;
		}

		return False;
	}

	public function put($group,NodeInterface $node)
	{
		throw new \RuntimeException('not yet implemented.');
	}
}