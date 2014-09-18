<?php
/**
 *  This file is part of the Unika-CMF project.
 *  AuthGuardProvider prevent brute force and other security issue
 *
 *  @license MIT
 *  @author Fajar Khairil
 */

namespace Unika\Provider;

class SessionServiceProvider extends \Silex\Provider\SessionServiceProvider
{
    public function register(\Pimple\Container $app)
	{
		parent::register($app);
        $app['session.storage.save_path'] = $app['config']->get('session.File.path');
        $app['SessionManager'] = new \Unika\Common\SessionWrapper($app);
        $app['session.storage.handler'] = $app['SessionManager']->getSession($app['config']['session.default']); 		
	}	
}