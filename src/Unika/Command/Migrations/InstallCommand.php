<?php

/**
 *	This file is part of the UnikaCMF project
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika\Command\Migrations;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Unika\Ext\Command;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;

class InstallCommand extends Command
{
    protected function configure()
    {
    	$this->setName('migrate:install')
    	->addOption('connection','c',InputOption::VALUE_OPTIONAL,'database connection to use, default connection will be used if not specified.')
    	->setDescription('Create the migration repository');
    }	

   protected function execute(InputInterface $input, OutputInterface $output)
   {
   	  $conn = $input->getOption('connection');
   	  if( null === $conn )
   	  	$conn = $this->container->config('database.default');

      if( $this->container['database']->schema($conn)->hasTable($this->container->config('database.migrations')) )
      {
          $confirmed = $this->getHelper('question')->ask(
            $input,
            $output,
            new ConfirmationQuestion('Migrations Table already exists, do you want to recreate it ? (y/n) : ',True)
          );        

          if( !$confirmed ){
            $this->comment($output,'Canceled by user.');
            return False;
          }          
      }

   	  $Migrator = new DatabaseMigrationRepository(
   	  	new \Illuminate\Database\ConnectionResolver($this->container['database']->getDatabaseManager()->getConnections()),
   	  	$this->container->config('database.migrations')
   	  );

   	  $Migrator->setSource($conn);
   	  $Migrator->createRepository();
      $this->info($output,'Migrator Repository successfully created..');
   }	
}