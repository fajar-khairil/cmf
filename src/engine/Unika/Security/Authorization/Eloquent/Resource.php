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
	protected $table = 'acos';

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

	public function getResourceId()
	{
		if( !$this->exists )
			throw new \RuntimeException('cannot get resource description when object not loaded.');

		return $this->getKey();
	}

	public function getResourceDescription()
	{
		if( !$this->exists )
			throw new \RuntimeException('cannot get resource description when object not loaded.');

		return $this->description;
	}
}