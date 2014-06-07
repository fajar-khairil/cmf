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

	public function __construct(\Application $app)
	{
		$this->app = $app;
	}

	/**
	 *
	 *	hash using HMAC with algo
	 */
	public function sign($secret_data)
	{
		return $app['signer']->sign($secret_data);
	}

	public function checkSign($hashes_data)
	{
		return $app['signer']->check($hashes_data);
	}

	public function extractSign($hashes_data)
	{
        if (!preg_match('/^(.*)(?:\?|&)_hash=(.+?)$/', $hashes_data, $matches)) {
            return false;
        }

        return array(
        	'secret_data' 	=>  $matches[1],
        	'secret_key'	=>	$matches[2]		
        );
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