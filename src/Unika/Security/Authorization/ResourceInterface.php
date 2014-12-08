<?php
/**
 *	This file is part of the Unika-CMF project.
 *	Resource Interface
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika\Security\Authorization;

Interface ResourceInterface{

	public function getResourceId();

	public function getResourceName();
} 