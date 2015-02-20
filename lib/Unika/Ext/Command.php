<?php

/**
 *	This file is part of the UnikaCMF project
 *  some parts are steal from laravel command	
 *
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika\Ext;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends SymfonyCommand
{
	protected $container = null;

    public function __construct($name = null,\Pimple\Container $container = null)
    {
    	if( null === $container )
    		$container = \Unika\Application::instance();

    	$this->setContainer($container);

    	parent::__construct($name);
    }

    public function setContainer(\Pimple\Container $container)
    {	
    	if( null === $this->container ){
        	$this->container = $container;
        }
    }

    /**
     * Write a string as information output.
     *
     * @param  string  $string
     * @return void
     */
    public function info(OutputInterface $output,$string)
    {
        $output->writeln("<info>$string</info>");
    }

    /**
     * Write a string as standard output.
     *
     * @param  string  $string
     * @return void
     */
    public function line(OutputInterface $output,$string)
    {
        $output->writeln($string);
    }

    /**
     * Write a string as comment output.
     *
     * @param  string  $string
     * @return void
     */
    public function comment(OutputInterface $output,$string)
    {
        $output->writeln("<comment>$string</comment>");
    }

    /**
     * Write a string as question output.
     *
     * @param  string  $string
     * @return void
     */
    public function question(OutputInterface $output,$string)
    {
        $output->writeln("<question>$string</question>");
    }

    /**
     * Write a string as error output.
     *
     * @param  string  $string
     * @return void
     */
    public function error(OutputInterface $output,$string)
    {
        $output->writeln("<error>$string</error>");
    }

    /**
     * Call another console command silently.
     *
     * @param  string  $command
     * @param  array   $arguments
     * @return integer
     */
    public function callSilent($command, array $arguments = array())
    {
        $instance = $this->getApplication()->find($command);

        $arguments['command'] = $command;

        return $instance->execute(new ArrayInput($arguments), new NullOutput);
    }
}