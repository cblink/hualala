<?php

namespace Cblink\Hualala;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class EncrypterServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param \Pimple\Container $app A container instance
     */
    public function register(Container $app)
    {
        $app['encrypter'] = function ($app) {
            return new Encrypter($app['config']);
        };
    }
}
