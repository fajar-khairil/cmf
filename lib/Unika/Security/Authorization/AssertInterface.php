<?php
/**
 *  This file is part of the UnikaCMF project.
 *  Authorization AssertInterface , intended checking resource access which require more logic
 *  
 *  @license MIT
 *  @author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika\Security\Authorization;

interface AssertInterface
{
    /**
     * Returns true if and only if the assertion conditions are met
     *
     * This method is passed the ACL, Role, Resource, and privilege to which the authorization query applies. If the
     * $role, $resource, or $privilege parameters are null, it means that the query applies to all Roles, Resources, or
     * privileges, respectively.
     *
     * @param  AclInterface             $acl
     * @param  mixed                    $role
     * @param  mixed                    $resource
     * @param  mixed string or array    operation
     *
     * @return boolean
     */
    public function assert(
        AclInterface $acl, 
        $role, 
        $resource,
        $operation);
}