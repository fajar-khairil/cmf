<?php
/**
 *	This file is part of the Unika-CMF project.
 *	Security Utils
 *	
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Security;

class Util
{

	protected $app;

	public function __construct(\Unika\Application $app)
	{
		$this->app = $app;
	}

	/**
	 *
	 *	hash using HMAC with algo
	 */
	public function sign($secret_data,$raw = False)
	{
		return hash_hmac($app['config']['app.sign_algo'],$secret_data,$this->app['config']['app.secret_key'],$raw);
	}

	public function verifySign()
	{
		throw new \RuntimeException('not yet implemented');
	}

	function encrypt($secret_data, $key = null) 
	{
		if( $key === null )
			$key = $this->app['config']['app.secret_key'];		

	    $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
	    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

	    return base64_encode(mcrypt_encrypt(
	    	MCRYPT_BLOWFISH, 
	    	$key, 
	    	utf8_encode($secret_data), 
	    	MCRYPT_MODE_ECB, 
	    	$iv
	    ));
	}

	/**
	 * Returns decrypted original string
	 */
	function decrypt($encrypted_data, $key = null) 
	{
		if( $key === null )
			$key = $this->app['config']['app.secret_key'];

	    $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);

	    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

	    return mcrypt_decrypt(
	    	MCRYPT_BLOWFISH, 
	    	$key, 
	    	base64_decode($encrypted_data), 
	    	MCRYPT_MODE_ECB,
	    	$iv
	    );	 
	}
}