<?php
/**
 *  This file is part of the Unika-CMF project.
 *  Config Repository extends Illuminate\Config\Repository
 *
 *  @license : MIT 
 *  @author  : Fajar Khairil
 */

namespace Unika\Common\Config;

Interface LoaderInterface extends \Illuminate\Config\LoaderInterface
{
	public function afterSet($env,$key,$value);
}