<?php

/**
 *	This file is part of the UnikaCMF project
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Unika\Ext\Command;

class CacheCommand extends Command
{
    protected function configure()
    {
    	$this->setName('cache:flush')->setDescription('flush the system cache.');
    }	

   protected function execute(InputInterface $input, OutputInterface $output)
   {
      $this->comment($output,'using default cache  '.get_class($this->container['cache']->getStore()));
      $this->info($output,'flushing system cache..');
      $this->container->cache->getStore()->flush();
   }
}