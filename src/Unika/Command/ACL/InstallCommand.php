<?php

/**
 *	This file is part of the UnikaCMF project
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika\Command\ACL;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Unika\Ext\Command;

class InstallCommand extends \Unika\Ext\Command
{
	protected function configure()
	{
	  $this->setName('acl:install')
	  ->addOption('connection','c',InputOption::VALUE_OPTIONAL,'Select conection to use',$this->container->config('database.default'))
	  ->setDescription('Install ACL Tables dependencies.');
	}	

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$this->info($output,'not yet implemented.');
	}
}