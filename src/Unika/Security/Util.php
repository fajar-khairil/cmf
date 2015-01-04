<?php
/**
 *	This file is part of the UnikaCMF project
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

 namespace Unika\Security;

 class Util implements \Unika\Security\PasswordHasherInterface
 {
      /**
     * Create a password hash from the supplied password and generator prefix
     *
     * @param string $password The password to hash
     * @param string $prefix   The prefix of the hashing function
     *
     * @return string The generated password hash
     */
 	public function createPasswordHash($password,$prefix = '$2a$',array $options = array())
 	{
 		$passwordLib = new \PasswordLib\PasswordLib();
 		
 		if( !isset($options['cost']) ){
 			$options['cost'] = 8;
 		}

 		return $passwordLib->createPasswordHash($password,$prefix,$options);
 	}

     /**
     * Verify a password against a supplied password hash
     *
     * @param string $password The supplied password to attempt to verify
     * @param string $hash     The valid hash to verify against
     *
     * @throws \RuntimeException If the hash is invalid or impossible to verify
     * @return boolean Is the password valid
     */
 	public function verifyPasswordHash($password, $hash)
 	{
 		$passwordLib = new \PasswordLib\PasswordLib();
 		return $passwordLib($password,$hash);
 	}

    /**
     * Generate a random token using base64 characters (a-zA-Z0-9./)
     *
     * @param int $size The number of characters in the generated output
     *
     * @return string The generated token string
     */
 	public function generateRandomString($length = 8)
 	{
 		$passwordLib = new \PasswordLib\PasswordLib();
 		return $passwordLib->getRandomToken($length);
 	}
 }