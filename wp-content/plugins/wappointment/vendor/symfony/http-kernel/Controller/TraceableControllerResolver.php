<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WappoVendor\Symfony\Component\HttpKernel\Controller;

use WappoVendor\Symfony\Component\HttpFoundation\Request;
use WappoVendor\Symfony\Component\Stopwatch\Stopwatch;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class TraceableControllerResolver implements \WappoVendor\Symfony\Component\HttpKernel\Controller\ControllerResolverInterface, \WappoVendor\Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface
{
    private $resolver;
    private $stopwatch;
    private $argumentResolver;
    public function __construct(\WappoVendor\Symfony\Component\HttpKernel\Controller\ControllerResolverInterface $resolver, \WappoVendor\Symfony\Component\Stopwatch\Stopwatch $stopwatch, \WappoVendor\Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface $argumentResolver = null)
    {
        $this->resolver = $resolver;
        $this->stopwatch = $stopwatch;
        $this->argumentResolver = $argumentResolver;
        // BC
        if (null === $this->argumentResolver) {
            $this->argumentResolver = $resolver;
        }
        if (!$this->argumentResolver instanceof \WappoVendor\Symfony\Component\HttpKernel\Controller\TraceableArgumentResolver) {
            $this->argumentResolver = new \WappoVendor\Symfony\Component\HttpKernel\Controller\TraceableArgumentResolver($this->argumentResolver, $this->stopwatch);
        }
    }
    /**
     * {@inheritdoc}
     */
    public function getController(\WappoVendor\Symfony\Component\HttpFoundation\Request $request)
    {
        $e = $this->stopwatch->start('controller.get_callable');
        $ret = $this->resolver->getController($request);
        $e->stop();
        return $ret;
    }
    /**
     * {@inheritdoc}
     *
     * @deprecated This method is deprecated as of 3.1 and will be removed in 4.0.
     */
    public function getArguments(\WappoVendor\Symfony\Component\HttpFoundation\Request $request, $controller)
    {
        @\trigger_error(\sprintf('The "%s()" method is deprecated as of 3.1 and will be removed in 4.0. Please use the %s instead.', __METHOD__, \WappoVendor\Symfony\Component\HttpKernel\Controller\TraceableArgumentResolver::class), \E_USER_DEPRECATED);
        $ret = $this->argumentResolver->getArguments($request, $controller);
        return $ret;
    }
}
