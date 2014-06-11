<?php

namespace Unika\Common\Config;

class Database implements implements \Illuminate\Config\LoaderInterface
{
	/**
	 * Load the given configuration group.
	 *
	 * @param  string  $environment
	 * @param  string  $group
	 * @param  string  $namespace
	 * @return array
	 */
	public function load($environment, $group, $namespace = null)
	{
		throw new \RuntimeException('not yet implemented');
	}

	/**
	 * Determine if the given configuration group exists.
	 *
	 * @param  string  $group
	 * @param  string  $namespace
	 * @return bool
	 */
	public function exists($group, $namespace = null)
	{
		throw new \RuntimeException('not yet implemented');
	}

	/**
	 * Add a new namespace to the loader.
	 *
	 * @param  string  $namespace
	 * @param  string  $hint
	 * @return void
	 */
	public function addNamespace($namespace, $hint)
	{
		throw new \RuntimeException('not yet implemented');
	}

	/**
	 * Returns all registered namespaces with the config
	 * loader.
	 *
	 * @return array
	 */
	public function getNamespaces()
	{
		throw new \RuntimeException('not yet implemented');
	}	

	/**
	 * Apply any cascades to an array of package options.
	 *
	 * @param  string  $environment
	 * @param  string  $package
	 * @param  string  $group
	 * @param  array   $items
	 * @return array
	 */
	public function cascadePackage($environment, $package, $group, $items)
	{
		throw new \RuntimeException('not yet implemented');
	}	
}