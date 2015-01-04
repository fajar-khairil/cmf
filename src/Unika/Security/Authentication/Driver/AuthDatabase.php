<?php
/**
 *	This file is part of the UnikaCMF project
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika\Security\Authentication\Driver;

use Unika\Security\Authentication\AuthDriverInterface;
use Unika\Security\Authentication\AuthUserInterface;
use Unika\Security\Authentication\AuthException;

class AuthDatabase implements AuthDriverInterface
{
	protected $app;
	protected $db;

	public function __construct(\Unika\Application $app)
	{
		$this->app = $app;
		$this->db = $this->app['database']->table( $this->app->config('auth.database.users_table') );
		$this->init();
	}

	protected function init()
	{
		$self = $this;
		$this->app['Illuminate.events']->listen('auth.success',function($auth,$credentials) use($self){
			$self->doOnSucess($auth,$credentials);
		});

		$this->app['Illuminate.events']->listen('auth.failure',function($credentials) use($self){
			$self->doOnFailure($credentials);
		});		

		$this->app['Illuminate.events']->listen('auth.logout',function($credentials) use($self){
			$self->doOnLogout($credentials);
		});				
	}

	protected function doOnFailure($credentials)
	{

	}

	protected function doOnLogout($credentials)
	{
		
	}

	protected function doOnSucess($auth,$credentials)
	{

	}

	/**
	 *	@param array or AuthUserInterface $credentials
	 *	@return AuthUserInteface
	 *	@throw AuthException
	 */
	public function authenticate($credentials)
	{
		if( is_array($credentials) )
		{
			$username = $credentials['username'];
		}
		elseif( \Unika\Util::classImplements($credentials,'AuthUserInterface') )
		{
			$credentialsObj = $credentials;
			$credentials = array(
				'username'	=> 	$credentialsObj->getUsername(),
				'password'	=> $credentialsObj->getPassword()
			);
			$username = $credentials['username'];	
			unset($credentialsObj);
		}
		else
		{
			throw new AuthException('Invalid Credentials supplied');
		}

		$user = $this->db->where('username','=',$username)->first();

		if( !$user )
		{
			// @todo : should we localized it?
			throw new AuthException('Invalid Username supplied');
		}

		$credentials['salt'] = $user['salt'];

		$passwordLibClass = $this->app->config('auth.password_hasher_class');
	
		if( !\Unika\Util::classImplements($passwordLibClass,'Unika\Security\PasswordHasherInterface') )
		{
			throw new \RuntimeException('invalid password_hasher_class please check your auth config.');
		}

		$passwordLib = new $passwordLibClass();

		return $passwordLib->verifyPasswordHash( $credentials['password'].$user['salt'],$user['pass'] );
	}
}