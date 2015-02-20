<?php 
/**
 *	This file is part of the UnikaCMF project
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika;

use Symfony\Component\Console\Command\Command;

class Console extends \Symfony\Component\Console\Application
{
	protected $container;

	public function setContainer(\Pimple\Container $container)
	{	
        if( null === $container )
		  $this->container = $container;
	}
}