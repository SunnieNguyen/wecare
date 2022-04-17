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
use WappoVendor\Symfony\Component\Debug\Exception\FlattenException;
use WappoVendor\Symfony\Component\EventDispatcher\EventDispatcherInterface;
use WappoVendor\Symfony\Component\EventDispatcher\EventSubscriberInterface;
use WappoVendor\Symfony\Component\HttpFoundation\Request;
use WappoVendor\Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use WappoVendor\Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use WappoVendor\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use WappoVendor\Symfony\Component\HttpKernel\HttpKernelInterface;
use WappoVendor\Symfony\Component\HttpKernel\KernelEvents;
use WappoVendor\Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
/**
 * ExceptionListener.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ExceptionListener implements \WappoVendor\Symfony\Component\EventDispatcher\EventSubscriberInterface
{
    protected $controller;
    protected $logger;
    protected $debug;
    public function __construct($controller, \WappoVendor\Psr\Log\LoggerInterface $logger = null, $debug = false)
    {
        $this->controller = $controller;
        $this->logger = $logger;
        $this->debug = $debug;
    }
    public function onKernelException(\WappoVendor\Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $request = $event->getRequest();
        $eventDispatcher = \func_num_args() > 2 ? \func_get_arg(2) : null;
        $this->logException($exception, \sprintf('Uncaught PHP Exception %s: "%s" at %s line %s', \get_class($exception), $exception->getMessage(), $exception->getFile(), $exception->getLine()));
        $request = $this->duplicateRequest($exception, $request);
        try {
            $response = $event->getKernel()->handle($request, \WappoVendor\Symfony\Component\HttpKernel\HttpKernelInterface::SUB_REQUEST, false);
        } catch (\Exception $e) {
            $this->logException($e, \sprintf('Exception thrown when handling an exception (%s: %s at %s line %s)', \get_class($e), $e->getMessage(), $e->getFile(), $e->getLine()));
            $prev = $e;
            do {
                if ($exception === ($wrapper = $prev)) {
                    throw $e;
                }
            } while ($prev = $wrapper->getPrevious());
            $prev = new \ReflectionProperty($wrapper instanceof \Exception ? \Exception::class : \Error::class, 'previous');
            $prev->setAccessible(true);
            $prev->setValue($wrapper, $exception);
            throw $e;
        }
        $event->setResponse($response);
        if ($this->debug && $eventDispatcher instanceof \WappoVendor\Symfony\Component\EventDispatcher\EventDispatcherInterface) {
            $cspRemovalListener = function (\WappoVendor\Symfony\Component\HttpKernel\Event\FilterResponseEvent $event) use(&$cspRemovalListener, $eventDispatcher) {
                $event->getResponse()->headers->remove('Content-Security-Policy');
                $eventDispatcher->removeListener(\WappoVendor\Symfony\Component\HttpKernel\KernelEvents::RESPONSE, $cspRemovalListener);
            };
            $eventDispatcher->addListener(\WappoVendor\Symfony\Component\HttpKernel\KernelEvents::RESPONSE, $cspRemovalListener, -128);
        }
    }
    public static function getSubscribedEvents()
    {
        return [\WappoVendor\Symfony\Component\HttpKernel\KernelEvents::EXCEPTION => ['onKernelException', -128]];
    }
    /**
     * Logs an exception.
     *
     * @param \Exception $exception The \Exception instance
     * @param string     $message   The error message to log
     */
    protected function logException(\Exception $exception, $message)
    {
        if (null !== $this->logger) {
            if (!$exception instanceof \WappoVendor\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface || $exception->getStatusCode() >= 500) {
                $this->logger->critical($message, ['exception' => $exception]);
            } else {
                $this->logger->error($message, ['exception' => $exception]);
            }
        }
    }
    /**
     * Clones the request for the exception.
     *
     * @param \Exception $exception The thrown exception
     * @param Request    $request   The original request
     *
     * @return Request The cloned request
     */
    protected function duplicateRequest(\Exception $exception, \WappoVendor\Symfony\Component\HttpFoundation\Request $request)
    {
        $attributes = ['_controller' => $this->controller, 'exception' => \WappoVendor\Symfony\Component\Debug\Exception\FlattenException::create($exception), 'logger' => $this->logger instanceof \WappoVendor\Symfony\Component\HttpKernel\Log\DebugLoggerInterface ? $this->logger : null];
        $request = $request->duplicate(null, null, $attributes);
        $request->setMethod('GET');
        return $request;
    }
}
