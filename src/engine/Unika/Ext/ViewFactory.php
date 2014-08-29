<?php
/**
 *	This file is part of the Unika-CMF project.
 *	View Factory
 *
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Ext;

class ViewFactory extends \Illuminate\View\Factory
{
	protected $extensions = array('blade' => 'blade', 'php' => 'php');
}