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

    public function setContainer(\Pimple\Container $container)
    {
        $this->container = $container;
    }
}