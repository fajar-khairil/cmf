<?php
/**
 * This file is part of the UnikaCMF project
 *
 * @author Fajar Khairil <fajar.khairil@gmail.com>
 * @license MIT
 */

namespace Unika\Interfaces;
use Unika\Console;

interface CommandProviderInterface
{
    /**
     *
     *  register command if any
     */
    public function addCommand(Console $app);
}