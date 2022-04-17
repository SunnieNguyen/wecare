<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WappoVendor\Symfony\Component\HttpKernel\Controller\ArgumentResolver;

use WappoVendor\Psr\Container\ContainerInterface;
use WappoVendor\Symfony\Component\HttpFoundation\Request;
use WappoVendor\Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use WappoVendor\Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
/**
 * Yields a service keyed by _controller and argument name.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
final class ServiceValueResolver implements \WappoVendor\Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface
{
    private $container;
    public function __construct(\WappoVendor\Psr\Container\ContainerInterface $container)
    {
        $this->container = $container;
    }
    /**
     * {@inheritdoc}
     */
    public function supports(\WappoVendor\Symfony\Component\HttpFoundation\Request $request, \WappoVendor\Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata $argument)
    {
        $controller = $request->attributes->get('_controller');
        if (\is_array($controller) && \is_callable($controller, true) && \is_string($controller[0])) {
            $controller = $controller[0] . '::' . $controller[1];
        } elseif (!\is_string($controller) || '' === $controller) {
            return false;
        }
        if ('\\' === $controller[0]) {
            $controller = \ltrim($controller, '\\');
        }
        if (!$this->container->has($controller) && false !== ($i = \strrpos($controller, ':'))) {
            $controller = \substr($controller, 0, $i) . \strtolower(\substr($controller, $i));
        }
        return $this->container->has($controller) && $this->container->get($controller)->has($argument->getName());
    }
    /**
     * {@inheritdoc}
     */
    public function resolve(\WappoVendor\Symfony\Component\HttpFoundation\Request $request, \WappoVendor\Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata $argument)
    {
        if (\is_array($controller = $request->attributes->get('_controller'))) {
            $controller = $controller[0] . '::' . $controller[1];
        }
        if ('\\' === $controller[0]) {
            $controller = \ltrim($controller, '\\');
        }
        if (!$this->container->has($controller)) {
            $i = \strrpos($controller, ':');
            $controller = \substr($controller, 0, $i) . \strtolower(\substr($controller, $i));
        }
        (yield $this->container->get($controller)->get($argument->getName()));
    }
}
