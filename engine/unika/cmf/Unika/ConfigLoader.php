<?php
/**
 *
 *  Config Loader
 *
 *  @license : MIT 
 *  @author  : Fajar Khairil
 */
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
			$tmp_file = '';
			foreach ($paths as $key => $value) 
			{   
				$x = $this->resolve($tmp_file.$value);
				if( $x !== False )
				{
					unset($tmp_file);
					$idx = $key + 1;
					$tmp = $x;
					break;
				}
				$tmp_file .= $value.DIRECTORY_SEPARATOR;
			}

			$key = array();
			for( $idx;$idx < count( $paths );$idx++ )
			{
				$key[] = $paths[$idx];
			}

			$helper = new Helper\Arr();

			if( !empty($key) )
				$this->_cache[$keys] = $helper->path($tmp,implode('.',$key),$default);
			else
				$this->_cache[$keys] = $tmp;			
		}

		return $this->_cache[$keys];
	}

	protected function resolve($value)
	{	
		if( is_dir($this->_base_path.DIRECTORY_SEPARATOR.$value) )
		{
			return False;
		}
		//print_r($value.'.yml<br>');
		if( is_file($this->_base_path.DIRECTORY_SEPARATOR.$value.'.php') )
		{
			return \Unika\Config\NativeReader::resolve($this->_base_path.DIRECTORY_SEPARATOR.$value.'.php');
		}

		if( is_file($this->_base_path.DIRECTORY_SEPARATOR.$value.'.yml') )
		{
			return \Unika\Config\YmlReader::resolve($this->_base_path.DIRECTORY_SEPARATOR.$value.'.yml');
		}		
	}
}