<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WappoVendor\Symfony\Component\HttpKernel\EventListener;

use WappoVendor\Psr\Container\ContainerInterface;
/**
 * Sets the session in the request.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @final since version 3.3
 */
class SessionListener extends \WappoVendor\Symfony\Component\HttpKernel\EventListener\AbstractSessionListener
{
    private $container;
    public function __construct(\WappoVendor\Psr\Container\ContainerInterface $container)
    {
        $this->container = $container;
    }
    protected function getSession()
    {
        if (!$this->container->has('session')) {
            return null;
        }
        return $this->container->get('session');
    }
}
