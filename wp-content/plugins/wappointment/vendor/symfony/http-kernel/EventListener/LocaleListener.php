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

use WappoVendor\Symfony\Component\EventDispatcher\EventSubscriberInterface;
use WappoVendor\Symfony\Component\HttpFoundation\Request;
use WappoVendor\Symfony\Component\HttpFoundation\RequestStack;
use WappoVendor\Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use WappoVendor\Symfony\Component\HttpKernel\Event\GetResponseEvent;
use WappoVendor\Symfony\Component\HttpKernel\KernelEvents;
use WappoVendor\Symfony\Component\Routing\RequestContextAwareInterface;
/**
 * Initializes the locale based on the current request.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class LocaleListener implements \WappoVendor\Symfony\Component\EventDispatcher\EventSubscriberInterface
{
    private $router;
    private $defaultLocale;
    private $requestStack;
    /**
     * @param RequestStack                      $requestStack  A RequestStack instance
     * @param string                            $defaultLocale The default locale
     * @param RequestContextAwareInterface|null $router        The router
     */
    public function __construct(\WappoVendor\Symfony\Component\HttpFoundation\RequestStack $requestStack, $defaultLocale = 'en', \WappoVendor\Symfony\Component\Routing\RequestContextAwareInterface $router = null)
    {
        $this->defaultLocale = $defaultLocale;
        $this->requestStack = $requestStack;
        $this->router = $router;
    }
    public function onKernelRequest(\WappoVendor\Symfony\Component\HttpKernel\Event\GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $request->setDefaultLocale($this->defaultLocale);
        $this->setLocale($request);
        $this->setRouterContext($request);
    }
    public function onKernelFinishRequest(\WappoVendor\Symfony\Component\HttpKernel\Event\FinishRequestEvent $event)
    {
        if (null !== ($parentRequest = $this->requestStack->getParentRequest())) {
            $this->setRouterContext($parentRequest);
        }
    }
    private function setLocale(\WappoVendor\Symfony\Component\HttpFoundation\Request $request)
    {
        if ($locale = $request->attributes->get('_locale')) {
            $request->setLocale($locale);
        }
    }
    private function setRouterContext(\WappoVendor\Symfony\Component\HttpFoundation\Request $request)
    {
        if (null !== $this->router) {
            $this->router->getContext()->setParameter('_locale', $request->getLocale());
        }
    }
    public static function getSubscribedEvents()
    {
        return [
            // must be registered after the Router to have access to the _locale
            \WappoVendor\Symfony\Component\HttpKernel\KernelEvents::REQUEST => [['onKernelRequest', 16]],
            \WappoVendor\Symfony\Component\HttpKernel\KernelEvents::FINISH_REQUEST => [['onKernelFinishRequest', 0]],
        ];
    }
}
