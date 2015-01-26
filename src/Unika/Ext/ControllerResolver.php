<?php

/**
 *  This file is part of the UnikaCMF project
 *  
 *  @license MIT
 *  @author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika\Ext;

use Pimple\Container;
use Silex\ControllerResolver as BaseResolver;

class ControllerResolver extends BaseResolver
{
    /**
     * Returns an instantiated controller
     *
     * @param string $class A class name
     *
     * @return object
     */
    protected function instantiateController($class)
    {
        return new $class($this->app);
    }
}
