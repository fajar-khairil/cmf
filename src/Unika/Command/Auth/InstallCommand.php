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
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Unika\Ext\Command;

class InstallCommand extends \Unika\Ext\Command
{
    protected function configure()
    {
      $this->setName('auth:install')
      ->addOption('connection','c',InputOption::VALUE_OPTIONAL,'Select conection to use',$this->container->config('database.default'))
      ->setDescription('Install Authentication Tables dependencies.');
    }	

   protected function execute(InputInterface $input, OutputInterface $output)
   {
      // get schema builder
      $schema = $this->container['database']->schema($input->getOption('connection'));
      $tableUsers = $this->container->config('auth.drivers.database.users_table');

      if( $schema->hasTable($tableUsers) )
      {
          $confirmed = $this->getHelper('question')->ask(
            $input,
            $output,
            new ConfirmationQuestion('Depenencies Table already exists, do you want to recreate it ? (y/n) : ',True)
          );

          if( True === $confirmed )
          {
              $this->info($output,'drop table users and session_info..');
              $schema->drop($tableUsers);
              $schema->drop($this->container->config('auth.drivers.database.session_info_table'));
          }
          else
          {
              $this->info($output,'Command Canceled by user');
              return False;
          }
      }

      $this->info($output,'creating tables...');
      \Unika\Security\Authentication\Driver\AuthDatabase::createAllTables($this->container,$schema);   
   }	
}