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
use Illuminate\Database\Migrations\MigrationCreator;
use Symfony\Component\Process\Process;

class MakeCommand extends BaseCommand
{

  protected $creator;

  protected function configure()
  {
    $this->setName('migrate:make')
    ->addArgument('name', InputArgument::REQUIRED, 'The name of the migration')
    ->addOption('connection','c',InputOption::VALUE_OPTIONAL,'database connection to use, default connection will be used if not specified.')
    ->addOption('create', null, InputOption::VALUE_OPTIONAL, 'The table to be created.')
    ->addOption('table', null, InputOption::VALUE_OPTIONAL, 'The table to migrate.')
    ->addOption('module', null, InputOption::VALUE_OPTIONAL, 'The module the migration belongs to.', null)
    ->setDescription('Create new migration file');

    $this->creator = new MigrationCreator($this->container['Illuminate.filesystem']);
  }	

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    /*$conn = $input->getOption('connection');
    if( null === $conn )
    	$conn = $this->container->config('database.default');

    // It's possible for the developer to specify the tables to modify in this
    // schema operation. The developer may also specify if this table needs
    // to be freshly created so we can create the appropriate migrations.
    $name = $input->getArgument('name');

    $table = $input->getOption('table');

    $create = $input->getOption('create');

    if ( ! $table && is_string($create)) $table = $create;

    // Now we are ready to write the migration out to disk. Once we've written
    // the migration out, we will dump-autoload for the entire framework to
    // make sure that the migrations are registered by the class loaders.
    $file = $this->writeMigration($name, $table, $create);
    $output->writeln("<info>Created Migration:</info> $file");*/

    //call composer dump-autoload
    $composer = new \Unika\Composer();
    $output->writeln('<info>'.$composer->command('dump-autoload')->getOutput().'</info>');
  }	

  /**
   * Write the migration file to disk.
   *
   * @param  string  $name
   * @param  string  $table
   * @param  bool    $create
   * @return string created file
   */
  protected function writeMigration($name, $table, $create)
  {
    $path = $this->getMigrationPath();

    $file = pathinfo($this->creator->create($name, $path, $table, $create), PATHINFO_FILENAME);

    return $file;
  }
}