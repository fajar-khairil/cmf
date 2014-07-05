<?php
/**
 *	This file is part of the Unika-CMF project.
 *	Acl Implementation
 *
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Security\Authorization;

class Acl implements AclInterface
{
    CONST WILDCARD = '*';
	
	protected $roleRegistry;
	protected $resourceRegistry;
	//cache
	protected $cache;
	protected $auth;

	public function __construct(
		RoleRegistryInterface $roleRegistry,
		ResourceRegistryInterface $resourceRegistry,
		\Illuminate\Cache\Repository $cache = null)
	{
		$this->roleRegistry = $roleRegistry;
		$this->resourceRegistry = $resourceRegistry;
		
		if( null !== $cache )
			$this->cache = $cache;
	}

	public function setAuth(\Unika\Security\Authentication\AuthInterface $auth)
	{
		$this->auth = $auth;
	}

	public function getResourceRegistry()
	{
		return $this->resourceRegistry;
	}

	public function getRoleRegistry()
	{
		return $this->roleRegistry;
	}

    /**
     * Returns true if and only if the Resource exists in the ACL
     *
     * The $resource parameter can either be a Resource or a Resource identifier.
     *
     * @param  Resource\ResourceInterface|string $resource
     * @return bool
     */
	public function hasResource($resource)
	{
		return $this->resourceRegistry->has($resource);
	}

    /**
     * Returns true if and only if the Role has access to the Resource
     *
     * The $role and $resource parameters may be references to, or the string identifiers for,
     * an existing Resource and Role combination.
     *
     * If either $role or $resource is null, then the query applies to all Roles or all Resources,
     * respectively. Both may be null to query whether the ACL has a "blacklist" rule
     * (allow everything to all). By default, Zend\Permissions\Acl creates a "whitelist" rule (deny
     * everything to all), and this method would return false unless this default has
     * been overridden (i.e., by executing $acl->allow()).
     *
     * If a $privilege is not provided, then this method returns false if and only if the
     * Role is denied access to at least one privilege upon the Resource. In other words, this
     * method returns true if and only if the Role is allowed all privileges on the Resource.
     *
     * This method checks Role inheritance using a depth-first traversal of the Role registry.
     * The highest priority parent (i.e., the parent most recently added) is checked first,
     * and its respective parents are checked similarly before the lower-priority parents of
     * the Role are checked.
     *
     * @param  Role\RoleInterface|string            $role
     * @param  Resource\ResourceInterface|string    $resource
     * @param  string                               $privilege
     * @return bool
     */
    public function isAllowed($role = null, $resource = null, $privilege = null)
    {
    	$roleId = $this->getRoleId($role);
    	$resourceId = $this->getResourceId($resource); 
    }

    protected function getResourceId($resource)
    {
    	if( $resource === null )
    	{
  			return static::WILDCARD;
    	}

		$resource = $this->resourceRegistry->get($resource);
		if( $resource === NULL )
			throw new AclException($resource.' Role not found.');

		$resource = $resource->getResourceId();

    	return $resource;
    }

    protected function getRoleId($role)
    {
    	if( $role === null )
    	{
  			if( ! $this->auth->check() )
  				throw new AclException('role no supplied and there are no logged in user on Auth.');

  			$user = $this->auth->user();
  			if( ! $user instanceof RoleInterface )
  				throw new AclException('Invalid Role on Auth.');
    		
    		$role = $user->getRoleId();unset($user);
    	}
    	else
    	{
    		$role = $this->roleRegistry->get($role);
    		if( $role === NULL )
    			throw new AclException($role.' Role not found.');

    		$role = $role->getRoleId();
    	}

    	return $role;
    }
}