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
		$schema = $this->container['database']->schema($input->getOption('connection'));
		
		if( $schema->hasTable($this->container->config('acl.drivers.Database.acl_table')) )
		{
			$confirmed = $this->getHelper('question')->ask(
			$input,
			$output,
			new ConfirmationQuestion('Depenencies Table already exists, do you want to recreate it ? (y/n) : ',True)
			);		

			if( !$confirmed ){
				$this->info($output,'canceled by user');
				return False;
			}	

			$this->info($output,'droping tables..');
			$schema->drop($this->container->config('acl.drivers.Database.role_table'));
			$schema->drop($this->container->config('acl.drivers.Database.resource_table'));
        	$schema->drop($this->container->config('acl.drivers.Database.acl_table'));
		}

		$this->info($output,'creating tables dependencies..');
		\Unika\Security\Authorization\Driver\Database\AclDriver::createTablesDependencies($this->container,$schema);
		$this->comment($output,'ACL table dependencies created.');
	}
}