<?php
/**
 *	This file is part of the Unika-CMF project.
 *	Resource Interface
 *	
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Security\Authorization;

Interface ResourceInterface{

	public function getResourceId();

	public function getResourceName();

	public function getResourceDescription();
} 