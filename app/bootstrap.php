<?php
/**
 *	Custom execution script
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

use Unika\Application;

$app = Application::instance();

$app['Illuminate.Memcached'] = new Illuminate\Cache\MemcachedConnector;

$app->register(new Unika\Provider\DatabaseServiceProvider());
$app->register(new Unika\Provider\CacheServiceProvider());