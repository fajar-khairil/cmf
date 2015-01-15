<?php
return array(
	'default'	=> 'master',//default connection
        'fetch'         => PDO::FETCH_ASSOC,

        'connections' => [

                'sqlite' => [
                        'driver'   => 'sqlite',
                        'database' => \Unika\Application::$ROOT_DIR.'/var/database.sqlite',
                        'prefix'   => '',
                ],

                'master' => [
                        'driver'    => 'mysql',
                        'host'      => 'localhost',
                        'database'  => 'cmf',
                        'username'  => 'masterkey',
                        'password'  => '',
                        'charset'   => 'utf8',
                        'collation' => 'utf8_unicode_ci',
                        'prefix'    => '',
                ],

                'pgsql' => [
                        'driver'   => 'pgsql',
                        'host'     => 'localhost',
                        'database' => 'forge',
                        'username' => 'forge',
                        'password' => '',
                        'charset'  => 'utf8',
                        'prefix'   => '',
                        'schema'   => 'public',
                ],
        ],

        /*
        |--------------------------------------------------------------------------
        | Migration Repository Table
        |--------------------------------------------------------------------------
        |
        | This table keeps track of all the migrations that have already run for
        | your application. Using this information, we can determine which of
        | the migrations on disk haven't actually been run in the database.
        |
        */

        'migrations' => 'migrations',

        'redis' => [

                'cluster' => false,

                'default' => [
                        'host'     => '127.0.0.1',
                        'port'     => 6379,
                        'database' => 0,
                ],

        ],
);