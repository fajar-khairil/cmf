<?php
/**
 *	This file is part of the UnikaCMF project
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika\Security\Authentication\Driver\Database;

use Unika\Security\Authentication\AuthDriverInterface;
use Unika\Security\Authentication\AuthUserInterface;
use Unika\Security\Authentication\AuthException;

class AuthDriver implements AuthDriverInterface;
{
	protected $app;
	protected $db;

	public function __construct(\Unika\Application $app)
	{
		$this->app = $app;
		$this->db = $this->app['database']->table( $this->app->config('auth.database.users_table') );
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
				'password'	=> $credentialsObj->getPassword(),
				'salt'		=> $credentialsObj->getSalt()
			);
			$username = $credentials['username'];	
			unset($credentialsObj);
		}
		else
		{
			throw new AuthException('Invalid Credentials supplied');
		}

		$user = $this->db->where('username','=',$username)->take(1)->get();
	
		if( !$user )
		{
			// @todo : should we localized it?
			throw new AuthException('Invalid Username supplied');
		}

		$passwordLibClass = $this->app->config('password_hasher_class');
		if( !\Unika\Util::classImplements($passwordLibClass,'\Unika\Security\PasswordHasherInterface') )
		{
			throw new \RuntimeException('invalid password_hasher_class please check your auth config.')
		}

		$passwordLib = new $passwordLibClass;

		return $passwordLib->verifyPasswordHash( $credentials['password'].$user['salt'],$user['password'] );
	}
}