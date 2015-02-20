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

class ViewCommand extends Command
{
    protected function configure()
    {
    	$this->setName('view:flush')->setDescription('flush the system views');
    }	

   protected function execute(InputInterface $input, OutputInterface $output)
   {
      $directories = $this->container['Illuminate.filesystem']->files(\Unika\Application::$ROOT_DIR.'/var/views');

      foreach ( $directories as $directory)
      {
        $this->info($output,'deleting '.$directory.'..');
        $this->container['Illuminate.filesystem']->delete($directory);
      }

      $this->comment($output,'done');
   }
}