<?php
/**
 *	This file is part of the Unika-CMF project.
 *	Acl Implementation
 *
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika\Security\Authorization;

class Acl
{	
	protected $roleRegistry;
	protected $resourceRegistry;
    protected $aclDriver;
	//cache
	protected $cache;
	protected $auth;

	public function __construct(
		RoleRegistryInterface $roleRegistry,
		ResourceRegistryInterface $resourceRegistry,
		AclDriverInterface $aclDriver)
	{
		$this->roleRegistry = $roleRegistry;
		$this->resourceRegistry = $resourceRegistry;		
		$this->aclDriver = $aclDriver;
	}

	public function setAuth(\Unika\Security\Authentication\AuthInterface $auth)
	{
		$this->auth = $auth;
	}

    protected function getAuth()
    {
        if( $this->auth === NULL )
            throw new AclException('Auth not set');

        return $this->auth;
    }

	public function addResource($name)
	{
	   $resource = $this->resourceRegistry->createResource($name);
	   return $this->resourceRegistry->addResource($resource);
    }

	public function addRole($name,$description)
	{
       $role = $this->roleRegistry->createRole(['name' => $name,'description' => $description]);
       return $this->roleRegistry->addRole($role);
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
     * @param  Resource\ResourceInterface|string    $resource
     * @param  string                               $privilege
     * @param  Callback                             $assertInterface
     * @param  Role\RoleInterface|string            $role
     * @return bool
     */
    public function isAllowed($resource, $operation = '*',AssertInterface $assert = null,$role = null)
    {         
    	$role = $this->getRole($role);
        if( !$role ){
            throw new AclException( 'cannot find Supplied Role '.$role );
        }

        $resource = $this->getResource($resource);

        if( $assert instanceof AssertInterface )
        {
            return $assert->assert($this,$role,$resource,$operations);
        }

        return $this->aclDriver->queryAcl($role->id,$resource,$operation);
    }

    public function deny($role,$resource,array $operation = array('*'))
    {
        return $this->setRules($role,$resource,$operation,False);
    }

    public function allow($role,$resource,array $operation = array('*'))
    {
        return $this->setRules($role,$resource,$operation,True);
    }

    protected function setRules($role,$resource,array $operations = array('*'),$allow)
    {
        $role = $this->getRole($role);
        $res = $this->getResource($resource); 
        
        if($res === NULL){
            $res = $this->resourceRegistry->addResource($this->resourceRegistry->createResource($resource));
        }

        $resourceId = $res->getResourceId();
        $this->aclDriver->setRules($role->getRoleId(),$resourceId,$operations,$allow);     
    }

    //return ResourceInterface
    protected function getResource($resource)
    {
    	if( $resource === null )
    	{
  			return static::WILDCARD;
    	}

		$resource = $this->resourceRegistry->getResource($resource);

    	return $resource;
    }

    protected function getRole($role)
    {
    	if( $role === null )
    	{
  			if( ! $this->getAuth()->check() )
  				throw new AclException('role no supplied and there are no logged in user on Auth.');

  			$user = $this->getAuth()->user();
  			if( ! $user instanceof RoleInterface )
  				throw new AclException('Invalid Role on Auth.');
    		
    		return $user->role;
    	}
    	else
    	{
    		$role = $this->roleRegistry->getRole($role);
    		if( $role === NULL )
    			throw new AclException($role.' Role not found.');
    		return $role;
    	}
    }
}