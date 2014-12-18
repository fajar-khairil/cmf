<?php
/**
 *	This file is part of the Unika-CMF project.
 *	Resource Interface
 *	
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika\Security\Authorization;
use Unika\Security\Authorization\ResourceInterface;

Interface ResourceRegistryInterface{

	public function addResource(Array $resource);

	public function removeResource($resource);

	public function hasResource($resource);

	public function allResource();

	public function getResource($resource);
} 