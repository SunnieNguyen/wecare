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

use WappoVendor\Psr\Log\LoggerInterface;
use WappoVendor\Symfony\Component\EventDispatcher\EventSubscriberInterface;
use WappoVendor\Symfony\Component\HttpFoundation\Request;
use WappoVendor\Symfony\Component\HttpFoundation\RequestStack;
use WappoVendor\Symfony\Component\HttpFoundation\Response;
use WappoVendor\Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use WappoVendor\Symfony\Component\HttpKernel\Event\GetResponseEvent;
use WappoVendor\Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use WappoVendor\Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use WappoVendor\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use WappoVendor\Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use WappoVendor\Symfony\Component\HttpKernel\Kernel;
use WappoVendor\Symfony\Component\HttpKernel\KernelEvents;
use WappoVendor\Symfony\Component\Routing\Exception\MethodNotAllowedException;
use WappoVendor\Symfony\Component\Routing\Exception\NoConfigurationException;
use WappoVendor\Symfony\Component\Routing\Exception\ResourceNotFoundException;
use WappoVendor\Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use WappoVendor\Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use WappoVendor\Symfony\Component\Routing\RequestContext;
use WappoVendor\Symfony\Component\Routing\RequestContextAwareInterface;
/**
 * Initializes the context from the request and sets request attributes based on a matching route.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
class RouterListener implements \WappoVendor\Symfony\Component\EventDispatcher\EventSubscriberInterface
{
    private $matcher;
    private $context;
    private $logger;
    private $requestStack;
    private $projectDir;
    private $debug;
    /**
     * @param UrlMatcherInterface|RequestMatcherInterface $matcher      The Url or Request matcher
     * @param RequestStack                                $requestStack A RequestStack instance
     * @param RequestContext|null                         $context      The RequestContext (can be null when $matcher implements RequestContextAwareInterface)
     * @param LoggerInterface|null                        $logger       The logger
     * @param string                                      $projectDir
     * @param bool                                        $debug
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($matcher, \WappoVendor\Symfony\Component\HttpFoundation\RequestStack $requestStack, \WappoVendor\Symfony\Component\Routing\RequestContext $context = null, \WappoVendor\Psr\Log\LoggerInterface $logger = null, $projectDir = null, $debug = true)
    {
        if (!$matcher instanceof \WappoVendor\Symfony\Component\Routing\Matcher\UrlMatcherInterface && !$matcher instanceof \WappoVendor\Symfony\Component\Routing\Matcher\RequestMatcherInterface) {
            throw new \InvalidArgumentException('Matcher must either implement UrlMatcherInterface or RequestMatcherInterface.');
        }
        if (null === $context && !$matcher instanceof \WappoVendor\Symfony\Component\Routing\RequestContextAwareInterface) {
            throw new \InvalidArgumentException('You must either pass a RequestContext or the matcher must implement RequestContextAwareInterface.');
        }
        $this->matcher = $matcher;
        $this->context = $context ?: $matcher->getContext();
        $this->requestStack = $requestStack;
        $this->logger = $logger;
        $this->projectDir = $projectDir;
        $this->debug = $debug;
    }
    private function setCurrentRequest(\WappoVendor\Symfony\Component\HttpFoundation\Request $request = null)
    {
        if (null !== $request) {
            try {
                $this->context->fromRequest($request);
            } catch (\UnexpectedValueException $e) {
                throw new \WappoVendor\Symfony\Component\HttpKernel\Exception\BadRequestHttpException($e->getMessage(), $e, $e->getCode());
            }
        }
    }
    /**
     * After a sub-request is done, we need to reset the routing context to the parent request so that the URL generator
     * operates on the correct context again.
     */
    public function onKernelFinishRequest(\WappoVendor\Symfony\Component\HttpKernel\Event\FinishRequestEvent $event)
    {
        $this->setCurrentRequest($this->requestStack->getParentRequest());
    }
    public function onKernelRequest(\WappoVendor\Symfony\Component\HttpKernel\Event\GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $this->setCurrentRequest($request);
        if ($request->attributes->has('_controller')) {
            // routing is already done
            return;
        }
        // add attributes based on the request (routing)
        try {
            // matching a request is more powerful than matching a URL path + context, so try that first
            if ($this->matcher instanceof \WappoVendor\Symfony\Component\Routing\Matcher\RequestMatcherInterface) {
                $parameters = $this->matcher->matchRequest($request);
            } else {
                $parameters = $this->matcher->match($request->getPathInfo());
            }
            if (null !== $this->logger) {
                $this->logger->info('Matched route "{route}".', ['route' => isset($parameters['_route']) ? $parameters['_route'] : 'n/a', 'route_parameters' => $parameters, 'request_uri' => $request->getUri(), 'method' => $request->getMethod()]);
            }
            $request->attributes->add($parameters);
            unset($parameters['_route'], $parameters['_controller']);
            $request->attributes->set('_route_params', $parameters);
        } catch (\WappoVendor\Symfony\Component\Routing\Exception\ResourceNotFoundException $e) {
            $message = \sprintf('No route found for "%s %s"', $request->getMethod(), $request->getPathInfo());
            if ($referer = $request->headers->get('referer')) {
                $message .= \sprintf(' (from "%s")', $referer);
            }
            throw new \WappoVendor\Symfony\Component\HttpKernel\Exception\NotFoundHttpException($message, $e);
        } catch (\WappoVendor\Symfony\Component\Routing\Exception\MethodNotAllowedException $e) {
            $message = \sprintf('No route found for "%s %s": Method Not Allowed (Allow: %s)', $request->getMethod(), $request->getPathInfo(), \implode(', ', $e->getAllowedMethods()));
            throw new \WappoVendor\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException($e->getAllowedMethods(), $message, $e);
        }
    }
    public function onKernelException(\WappoVendor\Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent $event)
    {
        if (!$this->debug || !($e = $event->getException()) instanceof \WappoVendor\Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            return;
        }
        if ($e->getPrevious() instanceof \WappoVendor\Symfony\Component\Routing\Exception\NoConfigurationException) {
            $event->setResponse($this->createWelcomeResponse());
        }
    }
    public static function getSubscribedEvents()
    {
        return [\WappoVendor\Symfony\Component\HttpKernel\KernelEvents::REQUEST => [['onKernelRequest', 32]], \WappoVendor\Symfony\Component\HttpKernel\KernelEvents::FINISH_REQUEST => [['onKernelFinishRequest', 0]], \WappoVendor\Symfony\Component\HttpKernel\KernelEvents::EXCEPTION => ['onKernelException', -64]];
    }
    private function createWelcomeResponse()
    {
        $version = \WappoVendor\Symfony\Component\HttpKernel\Kernel::VERSION;
        $baseDir = \realpath($this->projectDir) . \DIRECTORY_SEPARATOR;
        $docVersion = \substr(\WappoVendor\Symfony\Component\HttpKernel\Kernel::VERSION, 0, 3);
        \ob_start();
        include __DIR__ . '/../Resources/welcome.html.php';
        return new \WappoVendor\Symfony\Component\HttpFoundation\Response(\ob_get_clean(), \WappoVendor\Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND);
    }
}
