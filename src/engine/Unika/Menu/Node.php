<?php
/**
 *	This file is part of the Unika-CMF project.
 *	Node Menu
 *
 *	@license MIT
 *	@author Fajar Khairil
 */

namespace Unika\Menu;

class Node extends \Illuminate\Support\Fluent implements \Unika\Menu\NodeInterface
{
	protected $childs = array();
	
	public function __construct($attributes = array())
	{
		$defaults = array(
			'id'		=> null,
			'title'		=> null,
			'slug'		=> null,
			'target'	=> null,
			'parent_id'	=> null
		);

		parent::__construct( array_merge($defaults,$attributes) );
	}

	public function addChild(NodeInterface $child)
	{
		$this->childs[] = $child;
	}	

	public function setParent($parent)
	{
		if( $parent instanceof NodeInterface )
		{
			if( !$parent->id ){
				throw new \RuntimeException('Wrong Argument.');
			}

			$this->parent_id = $parent->id;
		}
		else
		{
			$this->parent_id = $parent;
		}
	}

	public function getChilds()
	{
		return $this->childs;
	}
}