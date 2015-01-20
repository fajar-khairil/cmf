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
use Unika\Ext\Command;

abstract class BaseCommand extends Command {

	/**
	 * Get the path to the migration directory.
	 *
	 * @return string
	 */
	protected function getMigrationPath($moduleName = null)
	{
		// If the module is in the list of migration paths we received we will put
		// the migrations in that path. Otherwise, we will assume the module is
		// is in the module directories and will place them in that location.
		if ( ! is_null($module))
		{
			return $this->container['path.module'].'/'.$module.'/resources/database/migrations';
		}

		return $this->container['path.base'].'/var/database/migrations';
	}

}
