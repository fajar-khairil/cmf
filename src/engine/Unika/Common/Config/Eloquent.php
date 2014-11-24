<?php
/**
 *	This file is part of the Unika-CMF project.
 *  Database Config Loader
 *
 *  @license : MIT 
 *  @author  : Fajar Khairil
 *
 */

namespace Unika\Common\Config;

class Eloquent implements LoaderInterface
{
	protected $cache;
	protected $app;
	protected $capsule;

	protected $setting_table;

	public function __construct(
		\Application $app,
		\Illuminate\Database\Capsule\Manager $capsule,
		\Illuminate\Cache\Repository $cache = null
		)
	{		
		$this->capsule = $capsule;

		$this->app = $app;
		$this->setting_table = $app['config']['setting.table'];
		
		if( $cache === null )
			$cache = $this->app['cache'];

		$this->cache = $cache;
	}

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
        $group = $this->getNamespacePrefix().'::'.$group;
	
		$query = $this->capsule->table($this->setting_table)
		->select('*')
		->where('environment',$environment)
		->where('group',$group);
		//->take(1)
		//->remember(5,md5('setting_'.$environment.'_'.$group));

		$rows = $query->get();
		if( count($rows) > 0 )
		{
			$result = array();
			foreach( $rows as $row )
			{
				array_set($result,$row['key'],$row['value']);
			}

			return $result;			
		}
		else
		{
			return array();
		}
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
		$group = $this->getNamespacePrefix.'::'.$group;

		$row = $this->capsule->table($this->setting_table)
		->select('*')
		->where('group',md5('setting_'.\Application::detectEnvironment().'_'.$group))
		//->take(1)
		//->remember(5,'setting_'.$group)
		->get();

		return (boolean)$row;
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
		return null;
	}

	/**
	 * Returns all registered namespaces with the config
	 * loader.
	 *
	 * @return array
	 */
	public function getNamespaces()
	{
		return array();
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
		$row = $this->capsule->table($this->setting_table)
		->select('*')
		->where('environment',$environment)
		->where('group',$this->getNamespacePrefix().'::'.$group)
		//->take(1)
		//->remember(5,md5('setting_'.$environment.'_'.$group))
		->get();

		if( count($row) > 0 )
			return array_merge($items, array_add(array(),$rows[0]['key'],$rows[0]['value']) );
		else
			return $items;
	}	

	public function afterSet($env,$key,$value)
	{
		$namespace_pos = strpos($key, '::');//namespace position

		if( $namespace_pos !== False )
		{			
			$module_name = substr($key, 0,$namespace_pos);
			$row_keys = explode('.', substr($key,$namespace_pos + 2));
			$group = $module_name.'::'.$row_keys[0];unset($row_keys[0]);		
		}
		else
		{
			$row_keys = explode('.', $key);
			$group = '*::'.$row_keys[0];unset($row_keys[0]);			
		}

		$real_key = implode('.', $row_keys);	

		$values = [
			'id'			=> md5($env.$group.$real_key),
			'environment'	=> $env,
			'group'			=> $group,
			'key'			=> $real_key,
			'value'			=> $value
		];
		
		$row = $this->capsule->table($this->setting_table)
				->where('id',$values['id'])		
				->get();

		if( count($row) > 0 )
		{
			return $row;		
		}
		else
		{
			return $this->capsule->table($this->setting_table)
				   ->insert( $values );
		}
	}

	protected function getNamespacePrefix($namespace)
	{
		return $namespace ?: '*';
	}
}