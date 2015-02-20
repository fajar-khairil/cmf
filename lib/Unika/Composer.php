<?php 
/**
 *	This file is part of the UnikaCMF project
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika;

use Symfony\Component\Process\Process;

/**
 *
 * composer wrapper
 */
class Composer
{ 
	//composer path
	protected $composer = null;
	protected $defaultCommand = null;

    public function command($command,$callback = null)
    {
        $composer = $this->getComposer();
        if( !$composer ){
        	throw new \RuntimeException('composer not found');
        }

        $process = new Process( "$composer $command" );
        $process->run($callback);

        return $process;
    }	

    public function setDefaultFindComposerCommand($defaultCommand)
    {
    	$this->defaultCommand = $defaultCommand;
    }

    public function getDefaultFindComposerCommand()
    {
    	return $this->defaultCommand;
    }

    /**
     *
     *  attempt to find composer path
     *  currently unix only
     *
     *  @return string or False when failure to find composer
     */
    public function getComposer($defaultCommand = null)
    {   
    	if( null === $this->composer )
    	{
	        if( (null === $defaultCommand) AND (null === $this->defaultCommand) ){
	            $this->defaultCommand = 'which composer';
	        }

	        $process = new Process($this->defaultCommand);
	        $process->run();
	
	        if( 0 === $process->getExitCode() ){
	            return trim($process->getOutput());
	        }

	        $possibleCommands = array(
	            'which composer.phar',
	            'ls composer.phar',
	            'ls composer',
	            'ls $HOME/bin/composer.phar',
	            'ls $HOME/bin/composer'
	        );

	        $command = 'which composer.phar';
	        $result = False;

	        foreach ($possibleCommands as $command) {
	           $process->setCommandLine($command);
	           $process->run();

	           if( 0 === $process->getExitCode() ){
	            $result = $process->getOutput();
	            break;
	           }
	        }
	        if( is_string($result) )
	        	$this->composer = trim($result);

	        return False;
        }

        return $this->composer;
    }
}