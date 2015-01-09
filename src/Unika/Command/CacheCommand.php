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
    	$this->setName('cache')
    		->setDefinition(array(
    			new InputArgument('clear', InputArgument::OPTIONAL, 'Cache Operation', 'all'),
    			new InputArgument('warm', InputArgument::OPTIONAL, 'Cache Operation', 'all'),
    		))
    		->setDescription('clear or warm the system cache.');
    }	

   protected function execute(InputInterface $input, OutputInterface $output)
   {
   		if( $input->getArgument('clear') )
   		{
   			$output->writeln('<info>using default cache  '.get_class($this->container['cache']->getStore()).'</info>');
   			$output->writeln('clearing system cache..');
   			$this->container['cache']->getStore()->flush();
   		}
   		elseif( $input->getArgument('warm') )
   		{
   			$output->writeln('warming up the cache..');
   		}
   		else
   		{
   			$output->writeln('<info>USAGE : cache --clear || cache --warm</info>');
   		}
   }
}