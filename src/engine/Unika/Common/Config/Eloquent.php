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

	protected $hints = array();
	protected $setting_table;

	public function __construct(
		\Application $app,
		\Illuminate\Database\Capsule\Manager $capsule,
		\Illuminate\Cache\Repository $cache = null
		)
	{		
		$this->capsule = $capsule;

		$this->app = $app;
		$this->setting_table = $app['config']['setting.Database.table'];
		
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

		if( is_string($namespace) )
			$group .= '::'.trim( array_get($this->hints,$namespace,'') );
		
		$rows = $this->capsule->table($this->setting_table)
		->select('*')
		->where('environment',$environment)
		->where('group',$group)
		->take(1)
		->remember(5)
		->get();
		if( count($rows) > 0 )
		{
			return array_add(array(),$rows[0]['key'],$rows[0]['value']);
		}
		else
			return array();
	}

	public function normalizeKeys($dots)
	{
		$dot_pos = strpos($dots,'.');
		$tmp = array();
		if( $dot_pos !== False )
		{
			$tmp[] = substr($dots, 0,$dot_pos);
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
		if( is_string($namespace) )
			$group .= '::'.trim( array_get($this->hints,$namespace,'') );

		$row = $this->capsule->table($this->setting_table)
		->select('*')
		->where('group',$group)
		->take(1)
		->remember(5)
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
		$this->hints[$namespaces] = trim($hint);
	}

	/**
	 * Returns all registered namespaces with the config
	 * loader.
	 *
	 * @return array
	 */
	public function getNamespaces()
	{
		return $this->hints;
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
		if( is_string($namespace) )
			$group .= '::'.trim( $package );

		$row = $this->capsule->table($this->setting_table)
		->select('*')
		->where('environment',$environment)
		->where('group',$group)
		->take(1)
		->remember(5)
		->get();

		if( count($row) > 0 )
			return array_merge($items, array_add(array(),$rows[0]['key'],$rows[0]['value']) );
		else
			return $items;
	}	

	public function afterSet($env,$key,$value)
	{
		$namespace_pos = strpos($key, '::');//namespace position
		$module_name = '';
		
		$row_keys = explode('.', substr($key,$namespace_pos + 2));
		
		$group = $row_keys[0];unset($row_keys[0]);
		$real_key = implode('.', $row_keys);		

		if( $namespace_pos !== False )
		{			
			$module_name = substr($key, 0,$namespace_pos);
			$group = $module_name.'::'.$group;
		}

		$values = [
			'id'			=> md5($env.$group.$real_key),
			'environment'	=> $env,
			'group'			=> $group,
			'key'			=> $real_key,
			'value'			=> $value
		];
		
		$row = $this->capsule->table($this->setting_table)
				->select('id')
				->where('id',$values['id'])
				->take(1)			
				->get();

		if( count($row) > 0 )
		{
			$setting_id = $values['id'];unset($values['id']);
			return $this->capsule->table($this->setting_table)
				   ->where('id',$setting_id)
				   ->update( $values );			
		}
		else
		{
			return $this->capsule->table($this->setting_table)
				   ->insert( $values );
		}
	}
}