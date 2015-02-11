<?php
/**
 * This file is part of the UnikaCMF project
 *
 * @author Fajar Khairil <fajar.khairil@gmail.com>
 * @license MIT
 */

namespace Unika\Provider;

use Pimple\Container;
use Unika\Interfaces\ServiceProviderInterface;

class CommonServiceProvider implements ServiceProviderInterface
{
	public function register(Container $app)
    {
        /** register console if we are on cli mode */
        if( 'cli' === PHP_SAPI ){
            $app['console'] = new \Unika\Console('UnikaCommander','0.1-dev');
            //dd(get_class( $app ));
            $app['console']->setContainer($app);
        }

        $app['util'] = new \Unika\Util;
        $app['sec.util'] = new \Unika\Security\Util;
        $app['helper.html'] = new \Unika\Helper\Html();
        $app['validator'] = new \Unika\Helper\Validator();
    }

    /**
     *
     *  return description of provider
     */
    public function getDescription()
    {
        return 'Common Utilities Service Provider';
    }

    /**
     *
     *  return array of service with each description
     */
    public function getServices()
    {
        return array(
            'util'   =>  'Unika\Util',
            'sec.util'  => 'Unika\Security\Util',
            'helper.html'   => 'Unika\Helper\Html'
        );
    }

    /**
     *
     *  return an array('author' => '','license' => '','url' => '');
     */
    public function getInfo()
    {
        return array(
            'name'      => 'Common Service',
            'author'    => 'Fajar Khairil',
            'license'   => 'MIT',
            'url'       => 'http://www.unikacreative.com/',
            'version'   => '0.1'
        );
    }
}