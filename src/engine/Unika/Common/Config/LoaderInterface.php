<?php
/**
 *  This file is part of the Unika-CMF project.
 *  LoaderInterface extends \Illuminate\Config\LoaderInterface
 *
 *  @license : MIT 
 *  @author  : Fajar Khairil
 */

namespace Unika\Common\Config;

Interface LoaderInterface extends \Illuminate\Config\LoaderInterface
{
	public function afterSet($env,$key,$value);
}