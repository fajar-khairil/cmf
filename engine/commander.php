<?php
/**
 *
 *	Unika Commander
 *
 *	@license MIT
 *	@author Fajar Khairil
 */

define('ENGINE_PATH', __DIR__);
define('APC_PRESENT',extension_loaded('apc') AND (boolean)ini_get('apc.enabled'));