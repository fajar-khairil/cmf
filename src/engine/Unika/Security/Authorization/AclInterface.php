<?php
/**
 *	This file is part of the Unika-CMF project.
 *	Role Interface
 *	
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Security\Authorization;

Interface AclInterface
{
    public function hasResource($resource);
    
    /**
     * Returns true if and only if the Role has access to the Resource
     *
     * @param  Role\RoleInterface|string            $role
     * @param  Resource\ResourceInterface|string    $resource
     * @param  string                               $privilege
     * @param  AssertInterface                      $assertCallback
     * @return bool
     */
    public function isAllowed($resource = null, $operation = null,$assert = null,$role = null);
}