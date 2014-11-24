<?php
/**
 *	This file is part of the Unika-CMF project.
 *	Menu renderer Interface
 *
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Menu;

Interface RendererInterface
{
	public function render(\Illuminate\Support\Collection $collection);
}