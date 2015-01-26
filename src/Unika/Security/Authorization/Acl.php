<?php
/**
 *	This file is part of the UnikaCMF project.
 *	Acl Implementation
 *
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika\Security\Authorization;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class Acl
{	
	protected $roleRegistry;
	protected $resourceRegistry;
  protected $aclDriver;

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

	public function setAuth(\Unika\Security\Authentication\Auth $auth)
	{
		$this->auth = $auth;
	}

  protected function getAuth()
  {
      if( $this->auth === NULL )
          throw new AccessDeniedHttpException('Auth not set');

      return $this->auth;
  }

	public function addResource($name,$description = 'no description given')
	{
	   return $this->resourceRegistry->addResource(['name' => $name,'description' => $description]);
  }

	public function addRole($name,$description = 'no description given')
	{
       return $this->roleRegistry->addRole(['name' => $name,'description' => $description]);
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

  public function hasRole($role)
  {
    return $this->roleRegistry->has($role);
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
  public function isGranted($resource, $operation = '*',$role = null,$assertion = null)
  {     
    if( !is_string($operation) ) throw new \RuntimeException('operation must be string');

    if( $assertion === null )
    {
      $role = $this->getRole($role);
      if( !$role['id'] ){ throw new AccessDeniedHttpException('Role not found.'); }

      $resource = $this->getResource($resource);
      if( !$resource['id'] ){ throw new AccessDeniedHttpException('Resource not found.'); }  

      return $this->aclDriver->queryAcl($role['id'],$resource['id'],$operation);        
    }
    elseif( $assertion instanceof AssertInterface  )
    {
      return (boolean)$assertion->assert($this,$role,$resource,$operations);
    }

    return False;
  }

  public function assert( \Closure $assert )
  {
    return $assert($this);
  }

  public function deny($role,$resource,array $operation = array('*'))
  {
      return $this->setRules($role,$resource,$operation,False);
  }

  public function grant($role,$resource,array $operation = array('*'))
  {
      return $this->setRules($role,$resource,$operation,True);
  }

  protected function setRules($role,$resource,array $operations = array('*'),$allow)
  {
      $role = $this->getRole($role);
      $res = $this->getResource($resource); 

      if($res === NULL){
          $res = $this->resourceRegistry->addResource(['id' => $resource]);
      }

      $this->aclDriver->setRules($role['id'],$res['id'],$operations,$allow);     
  }

  //return ResourceInterface
  protected function getResource($resource)
  {
    return $this->resourceRegistry->getResource($resource);
  }

  protected function getRole($role)
  {
    if( $role === null )
    {
      if( ! $this->getAuth()->check() )
        return 0;// 0 mean public/anonymous

      $user = $this->getAuth()->user();
      $role = $user->role_id;
    }

    return $this->roleRegistry->getRole($role);
  }
}