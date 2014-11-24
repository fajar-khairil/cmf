<?php
/**
 *	This file is part of the Unika-CMF project.
 *	Menu DriverInterface
 *
 *	@license MIT
 *	@author Fajar Khairil
 */


namespace Unika\Menu;

interface DriverInterface
{
	//return collection of node
	public function buildTree($parentId);

	public function remove($node);

	public function destroyAllChilds($group);

	public function put($group,NodeInterface $node);

	//return boolean
	public function groupExists($group);
}