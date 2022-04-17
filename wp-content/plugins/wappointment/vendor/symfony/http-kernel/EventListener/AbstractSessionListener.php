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
use WappoVendor\Symfony\Component\HttpFoundation\Session\Session;
use WappoVendor\Symfony\Component\HttpFoundation\Session\SessionInterface;
use WappoVendor\Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use WappoVendor\Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use WappoVendor\Symfony\Component\HttpKernel\Event\GetResponseEvent;
use WappoVendor\Symfony\Component\HttpKernel\KernelEvents;
/**
 * Sets the session in the request.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
abstract class AbstractSessionListener implements \WappoVendor\Symfony\Component\EventDispatcher\EventSubscriberInterface
{
    private $sessionUsageStack = [];
    public function onKernelRequest(\WappoVendor\Symfony\Component\HttpKernel\Event\GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }
        $request = $event->getRequest();
        $session = $this->getSession();
        $this->sessionUsageStack[] = $session instanceof \WappoVendor\Symfony\Component\HttpFoundation\Session\Session ? $session->getUsageIndex() : null;
        if (null === $session || $request->hasSession()) {
            return;
        }
        $request->setSession($session);
    }
    public function onKernelResponse(\WappoVendor\Symfony\Component\HttpKernel\Event\FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }
        if (!($session = $event->getRequest()->getSession())) {
            return;
        }
        if ($session instanceof \WappoVendor\Symfony\Component\HttpFoundation\Session\Session ? $session->getUsageIndex() !== \end($this->sessionUsageStack) : $session->isStarted()) {
            $event->getResponse()->setExpires(new \DateTime())->setPrivate()->setMaxAge(0)->headers->addCacheControlDirective('must-revalidate');
        }
    }
    /**
     * @internal
     */
    public function onFinishRequest(\WappoVendor\Symfony\Component\HttpKernel\Event\FinishRequestEvent $event)
    {
        if ($event->isMasterRequest()) {
            \array_pop($this->sessionUsageStack);
        }
    }
    public static function getSubscribedEvents()
    {
        return [
            \WappoVendor\Symfony\Component\HttpKernel\KernelEvents::REQUEST => ['onKernelRequest', 128],
            // low priority to come after regular response listeners, same as SaveSessionListener
            \WappoVendor\Symfony\Component\HttpKernel\KernelEvents::RESPONSE => ['onKernelResponse', -1000],
            \WappoVendor\Symfony\Component\HttpKernel\KernelEvents::FINISH_REQUEST => ['onFinishRequest'],
        ];
    }
    /**
     * Gets the session object.
     *
     * @return SessionInterface|null A SessionInterface instance or null if no session is available
     */
    protected abstract function getSession();
}
