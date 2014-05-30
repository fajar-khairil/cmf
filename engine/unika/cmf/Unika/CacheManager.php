<?php

namespace Unika;

class CacheManager
{
	protected $_app;

	public function __construct(Application $app)
	{
		$this->_app = $app;
	}

    public function getCache($cache_type)
    {
        $cache_prefix = $this->_app['config_loader']->get('cache.prefix');
        switch ($cache_type) {
             case 'Apc':
                return $this->repository(new \Illuminate\Cache\ApcStore(new \Illuminate\Cache\ApcWrapper, $cache_prefix ));
                break;    
            case 'Memcached':
                $memcached = new \Memcached;
                
                $memcached->addServers( $this->_app['config_loader']->get('cache.Memcached') );

                if ($memcached->getVersion() === false)
                {
                    throw new \RuntimeException("Could not establish Memcached connection.");
                }

                return $this->repository( new \Illuminate\Cache\MemcachedStore( $memcached, $cache_prefix ) );
                break;
             case 'File':
                return $this->repository(new \Illuminate\Cache\FileStore(new \Illuminate\Filesystem, $this->_app['tmp_dir'] ));
                break;           
            case 'Arr' :
                return $this->repository(new \Illuminate\Cache\ArrayStore);
                break;
            case 'Redis' : 
                return $this->repository(new \Illuminate\Cache\RedisStore(new \Illuminate\Redis\Database(), $cache_prefix ));
                break;
            default:
                throw new \RuntimeException('invalid cache type.');
                break;
        }        
    }

    protected function repository(\Illuminate\Cache\StoreInterface $store)
    {
        return new \Illuminate\Cache\Repository($store);
    }
}