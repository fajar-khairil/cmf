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
          $answer = $this->getHelper('question')->ask(
            $input,
            $output,
            new ConfirmationQuestion('Table '.$tableUsers.' already exists, do you want to recreate it ? (y/n) : ',True)
          );

          if( True === $answer )
          {
              $output->writeln('drop table '.$tableUsers.'..');
              $schema->drop($tableUsers);
          }
          else
          {
              $output->writeln('<info>Command Canceled by user</info>');
              return False;
          }
      }

      $output->writeln('creating table '.$tableUsers.'..');
      \Unika\Security\Authentication\Driver\AuthDatabase::createUsersTable($this->container,$schema);   
   }	
}