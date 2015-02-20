<?php

/**
 *  This file is part of the Unika-CMF project
 *  modify default silex  TranslationServiceProvider
 *
 *  @license MIT
 *  @author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika\Ext;

use Pimple\Container;
use Symfony\Component\Translation\Translator as BaseTranslator;
use Symfony\Component\Translation\MessageSelector;

/**
 * Translator that gets the current locale from the container.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Translator extends BaseTranslator
{
    protected $app;

    public function __construct(Container $app, MessageSelector $selector,$cacheDir = null)
    {
        $this->app = $app;

        parent::__construct(null, $selector,$cacheDir);
    }

    public function getLocale()
    {
        return $this->app['locale'];
    }

    public function setLocale($locale)
    {
        if (null === $locale) {
            return;
        }

        $this->app['locale'] = $locale;

        parent::setLocale($locale);
    }
}
