<?php

/**
 *	This file is part of the UnikaCMF project
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika\Command\Auth;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Unika\Ext\Command;

class InstallCommand extends Command
{
    protected function configure()
    {
      $this->setName('auth:install')
      ->addOption('connection','c',InputOption::VALUE_OPTIONAL,'Select coneection to use',$this->container->config('database.default'))
      ->setDescription('Authentication Command');
    }	

   protected function execute(InputInterface $input, OutputInterface $output)
   {
      $connName = $input->getOption('connection');
      // get schema builder
      $schema = $this->container['database']->schema();
      $tableUsers = $this->container->config('auth.database.users_table');

      if( $schema->hasTable($tableUsers) )
      {
          $input->setInteractive(True);

          $qConfirm = new \Symfony\Component\Console\Question\ConfirmationQuestion($tableUsers.' already exists do you want to recreate it?',True);

          /*$schema->drop($tableUsers);
          $schema->create($tableUsers,function($blueprint){

          $blueprint->integer('id',True,True);
          $blueprint->string('firstname');
          $blueprint->string('lastname')->nullable();
          $blueprint->string('username');
          $blueprint->string('primary_email');
          $blueprint->string('pass');
          $blueprint->string('salt',128);
          
          $blueprint->tinyInteger('active');

          $blueprint->dateTime('last_login')->nullable();
          $blueprint->tinyInteger('last_failed_count')->nullable();
          $blueprint->integer('role_id');

          $blueprint->nullableTimestamps();

        });*/
      }
   }	
}