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
	public function buildTree($group)
	{

	}

	public function remove($node)
	{

	}

	public function destroy($group)
	{

	}

	public function put(NodeInterface $node)
	{
		$menu = new \Unika\Menu\Menu($node);
		$menu->save();
	}
}