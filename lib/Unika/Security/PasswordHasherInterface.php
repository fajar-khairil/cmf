<?php
/**
 *	This file is part of the UnikaCMF project
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika\Security;

 interface PasswordHasherInterface
 {
     /**
     * Create a password hash from the supplied password and generator prefix
     *
     * @param string $password The password to hash
     * @param string $prefix   The prefix of the hashing function
     *
     * @return string The generated password hash
     */
 	public function createPasswordHash($password,$prefix = '$2a$',array $options = array());

     /**
     * Verify a password against a supplied password hash
     *
     * @param string $password The supplied password to attempt to verify
     * @param string $hash     The valid hash to verify against
     *
     * @throws \RuntimeException If the hash is invalid or impossible to verify
     * @return boolean Is the password valid
     */
 	public function verifyPasswordHash($password, $hash);
 }