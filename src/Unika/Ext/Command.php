<?php

/**
 *	This file is part of the UnikaCMF project
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika\Ext;

use Symfony\Component\Console\Command\Command as SymfonyCommand;

class Command extends SymfonyCommand
{
	protected $container = null;

    public function __construct($name = null,\Pimple\Container $container = null)
    {
    	if( null === $container )
    		$container = \Unika\Application::instance();

    	$this->setContainer($container);

    	parent::__construct($name);
    }

    public function setContainer(\Pimple\Container $container)
    {	
    	if( null === $this->container ){
        	$this->container = $container;
        }
    }
}