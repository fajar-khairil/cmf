<?php
/**
 *	This file is part of the Unika-CMF project.
 *	Menu Node Interface
 *
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Menu;

Interface NodeInterface
{
	public function addChild(NodeInterface $child);

	public function setParent($parent);

	public function getChilds();
}