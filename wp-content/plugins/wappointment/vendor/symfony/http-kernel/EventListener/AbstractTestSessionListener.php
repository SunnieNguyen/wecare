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
use WappoVendor\Symfony\Component\HttpFoundation\Cookie;
use WappoVendor\Symfony\Component\HttpFoundation\Session\Session;
use WappoVendor\Symfony\Component\HttpFoundation\Session\SessionInterface;
use WappoVendor\Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use WappoVendor\Symfony\Component\HttpKernel\Event\GetResponseEvent;
use WappoVendor\Symfony\Component\HttpKernel\KernelEvents;
/**
 * TestSessionListener.
 *
 * Saves session in test environment.
 *
 * @author Bulat Shakirzyanov <mallluhuct@gmail.com>
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class AbstractTestSessionListener implements \WappoVendor\Symfony\Component\EventDispatcher\EventSubscriberInterface
{
    private $sessionId;
    public function onKernelRequest(\WappoVendor\Symfony\Component\HttpKernel\Event\GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }
        // bootstrap the session
        $session = $this->getSession();
        if (!$session) {
            return;
        }
        $cookies = $event->getRequest()->cookies;
        if ($cookies->has($session->getName())) {
            $this->sessionId = $cookies->get($session->getName());
            $session->setId($this->sessionId);
        }
    }
    /**
     * Checks if session was initialized and saves if current request is master
     * Runs on 'kernel.response' in test environment.
     */
    public function onKernelResponse(\WappoVendor\Symfony\Component\HttpKernel\Event\FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }
        if (!($session = $event->getRequest()->getSession())) {
            return;
        }
        if ($wasStarted = $session->isStarted()) {
            $session->save();
        }
        if ($session instanceof \WappoVendor\Symfony\Component\HttpFoundation\Session\Session ? !$session->isEmpty() || null !== $this->sessionId && $session->getId() !== $this->sessionId : $wasStarted) {
            $params = \session_get_cookie_params();
            foreach ($event->getResponse()->headers->getCookies() as $cookie) {
                if ($session->getName() === $cookie->getName() && $params['path'] === $cookie->getPath() && $params['domain'] == $cookie->getDomain()) {
                    return;
                }
            }
            $event->getResponse()->headers->setCookie(new \WappoVendor\Symfony\Component\HttpFoundation\Cookie($session->getName(), $session->getId(), 0 === $params['lifetime'] ? 0 : \time() + $params['lifetime'], $params['path'], $params['domain'], $params['secure'], $params['httponly']));
            $this->sessionId = $session->getId();
        }
    }
    public static function getSubscribedEvents()
    {
        return [\WappoVendor\Symfony\Component\HttpKernel\KernelEvents::REQUEST => ['onKernelRequest', 192], \WappoVendor\Symfony\Component\HttpKernel\KernelEvents::RESPONSE => ['onKernelResponse', -128]];
    }
    /**
     * Gets the session object.
     *
     * @return SessionInterface|null A SessionInterface instance or null if no session is available
     */
    protected abstract function getSession();
}
