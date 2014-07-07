<?php
/**
 *	This file is part of the Unika-CMF project.
 *	View Factory
 *
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Menu\Eloquent;

use Kalnoy\Nestedset\Node;

class Menu extends Node
{
	protected $table = 'menus';
	protected $guarded = array('rgt','lft');	
}