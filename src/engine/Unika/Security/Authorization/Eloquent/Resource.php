<?php
/**
 *	This file is part of the Unika-CMF project.
 *	Authorization\Resource Eloquent Implementation
 *	
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Security\Authorization\Eloquent;

use Kalnoy\Nestedset\Node;
use Unika\Security\Authorization\ResourceInterface;

class Resource extends Node implements ResourceInterface
{
	protected $table = 'aros';
	protected $guarded = array('lft', 'rgt');
	protected $fillable = array('name','description');
    /**
     * The name of "lft" column.
     *
     * @var string 
     */
    const LFT = 'lft';

    /**
     * The name of "rgt" column.
     *
     * @var string 
     */
    const RGT = 'rgt';	

	public function __construct(array $attributes = array())
	{
		parent::__construct($attributes);
		$app = \Application::instance();
		$this->table = $app['config']['acl.eloquent.resource_table'];
	}

	public function getResourceId()
	{

		return $this->getKey();
	}

	public function getResourceName()
	{
		return $this->name;
	}

	public function getResourceDescription()
	{
		return $this->description;
	}
}