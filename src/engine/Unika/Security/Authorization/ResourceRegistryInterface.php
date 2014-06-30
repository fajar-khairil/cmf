<?php
/**
 *	This file is part of the Unika-CMF project.
 *	Resource Interface
 *	
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Security\Authorization;

Interface ResourceRegistryInterface{

	public function add(ResourceInterface $resource);

	public function remove($resource);

	public function removeAll();

	public function isInherit($childResource,$parentResource);

	public function has($resource);

	public function all();

	public function get($resource);

	public function getParent($resource);
} 