<?php

namespace Unika;

class ConfigLoader
{
	protected $_base_path;
	protected $_cache;//local cache

	public function __construct($path)
	{
		if ( !is_dir($path) )
		{
			throw new \RuntimeException('Argument must be valid directory.');
		}

		$this->_base_path = realpath($path);  
	}

	public function get($keys,$default = null)
	{
		if( !isset( $this->_cache[$keys] ) )
		{
			$paths = explode('.',$keys);
			$tmp = array();
			$idx = 0;
			foreach ($paths as $key => $value) 
			{   
				$x = $this->resolve($value);
				if( $x !== False )
				{
					$idx = $key + 1;
					$tmp = $x;
					break;
				}
			}

			$key = array();
			for( $idx;$idx < count( $paths );$idx++ )
			{
				$key[] = $paths[$idx];
			}

			$helper = new Helper\Arr();

			$this->_cache[$keys] = $helper->path($tmp,implode('.',$key),$default);			
		}

		return $this->_cache[$keys];
	}

	protected function resolve($value)
	{	
		if( is_dir($this->_base_path.DIRECTORY_SEPARATOR.$value) )
		{
			return False;
		}

		if( is_file($this->_base_path.DIRECTORY_SEPARATOR.$value.'.php') )
		{
			return require $this->_base_path.DIRECTORY_SEPARATOR.$value.'.php';
		}
	}
}