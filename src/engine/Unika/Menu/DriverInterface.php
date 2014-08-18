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
	public function buildTree($group);

	public function remove($node);

	public function destroy($group);

	public function put(NodeInterface $node);
} 