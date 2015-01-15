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
		$this->container = $container;
	}

    public function add(Command $command)
    {
    	if( $command instanceof \Unika\Ext\Command )
    		$command->setContainer($this->container);
    	return parent::add($command);
    }
}