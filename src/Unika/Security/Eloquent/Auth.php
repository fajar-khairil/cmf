<?php
/**
 *	Unika-CMF Project
 *	default Auth Service Implementation
 *	
 *	@license MIT
 *	@author Fajar Khairil
 *
 *	todo : don't hardcode primary_key
 */

namespace Unika\Security\Eloquent;

use Unika\Security\Authentication\AuthInterface;
use Unika\Security\Authentication\AuthUserInterface;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder as hash;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Auth implements AuthInterface
{
	protected $app;

	public function __construct()
	{
		$this->app = \Unika\Bag::instance();
	}

	/**
	 *
	 *	login attempt
	 *
	 *	@param $credentials array, ussualy its username and password but it can be anything
	 * 	@return boolean
	 */
	public function attempt(array $credentials , $remember = False/*,$restrict_ip = False*/, $expired = 0)
	{
		$result = $this->_checkCredentials( $credentials );

		if( $result )
		{
			$this->app['session']->set( $this->app['config']['auth.session_name'] , $result );

			if( $remember === True )
			{
				//set remember cookie
				$cookies = $this->app['config'][ 'cookie.'.$this->app['config']['auth.cookie_remember'] ];
				$cookies['expired'] = $expired;
				$remember_token = $this->app['PasswordLib']->getRandomToken(32);
				$cookies['value'] = hash_hmac('ripemd160',$remember_token,$this->app['config']['app.secret_key']);
				$remember_cookie = $this->app['cookie']->cookie($cookies);

				//update user
				$this->_updateUser($result['id'],[
					'remember_token'		=>	$remember_token,
					'last_failed_count'		=>  0
				]);				

				$this->app->after(function($request,$response) use($remember_cookie){
					$response->headers->setCookie($remember_cookie);
				});
			}
		}	

		return (boolean)$result;	
	}

	/**
	 *
	 *	update user row
	 */
	protected function _updateUser($id,array $values)
	{
		$capsule = $this->app['Capsule'];
		$values['last_login'] = date('Y-m-d H:i:s',time());
		$query = $capsule::table($this->app['config']['auth.Eloquent.user_table'])
				->where('id',$id)
				->update($values);	
	}

	/**
	 *
	 *	internal function check supplied credentials
	 *	@return user array if success return False if failed, use by validate and attempt
	 */
	protected function _checkCredentials( array $credentials )
	{
		$capsule = $this->app['Capsule'];
		$query = $capsule::table($this->app['config']['auth.Eloquent.user_table'])
				->select('*');

		if( isset($credentials['password']) ){
			$credentials['pass'] = $credentials['password'];
			unset($credentials['password']);
		}	

		$pass = $credentials['pass'];unset($credentials['pass']);

		foreach( $credentials as $col=>$value )
		{
			$query->where($col,$value);
		}

		$row = $query->take(1)->get();

		$result = False;

		if( !empty($row) )
		{	
			if( $this->app['PasswordLib']->verifyPasswordHash($pass.$row[0]['salt'],$row[0]['pass']) )
				$result = $row[0];
		}

		return $result;
	}

	/**
	 *
	 *	allows you to validate a user's credentials without actually logging them into the application
	 *
	 *	@return boolean
	 */
	public function validate(array $credentials)
	{
		return (boolean)$this->_checkCredentials($credentials);
	}

	/**
	 *
	 *	single request login no session or cookie
	 */
	public function once(array $credentials)
	{

	}

	//determine if user already loggedin
	public function check()
	{

	}

	//logout user
	public function logout()
	{

	}

	//login user manually
	public function forceLogin(AuthUserInterface $user)
	{

	}

	/**
	 *
	 *	@return User Object null if user not already loggedin
	 */
	public function user()
	{

	}
}