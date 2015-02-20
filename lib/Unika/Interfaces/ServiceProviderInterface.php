<?php
/**
 * This file is part of the UnikaCMF project
 *
 * @author Fajar Khairil <fajar.khairil@gmail.com>
 * @license MIT
 */

namespace Unika\Interfaces;

interface ServiceProviderInterface extends \Pimple\ServiceProviderInterface
{
	/**
	 *
	 *	return description of provider
	 */
	public function getDescription();

	/**
	 *
	 *	return array of service with each description
	 */
	public function getServices();

	/**
	 *
	 *	return an array('author' => '','license' => '','url' => '');
	 */
	public function getInfo();
}