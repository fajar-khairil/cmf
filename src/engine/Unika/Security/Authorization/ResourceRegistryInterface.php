<?php
/**
 *	This file is part of the Unika-CMF project.
 *	Resource Interface
 *	
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Security\Authorization;
use Unika\Security\Authorization\ResourceInterface;

Interface ResourceRegistryInterface{

	public function add(ResourceInterface $resource,array $permission = array('*'));

	public function remove($resource);

	public function removeAll();

	public function has($resource);

	public function all();

	public function get($resource);
} 