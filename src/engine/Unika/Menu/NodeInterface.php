<?php
/**
 *	This file is part of the Unika-CMF project.
 *	NodeMenuInterface
 *
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Menu;

Interface NodeInterface
{
	public function getNodeId();

	public function getTarget();

	public function getTitle();

	public function getParent();

	public function getOrder();

	public function addChild();
}