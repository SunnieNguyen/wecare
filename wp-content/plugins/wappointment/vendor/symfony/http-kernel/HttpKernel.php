<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WappoVendor\Symfony\Component\HttpKernel;

use WappoVendor\Symfony\Component\EventDispatcher\EventDispatcherInterface;
use WappoVendor\Symfony\Component\HttpFoundation\Exception\RequestExceptionInterface;
use WappoVendor\Symfony\Component\HttpFoundation\Request;
use WappoVendor\Symfony\Component\HttpFoundation\RequestStack;
use WappoVendor\Symfony\Component\HttpFoundation\Response;
use WappoVendor\Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use WappoVendor\Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use WappoVendor\Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use WappoVendor\Symfony\Component\HttpKernel\Event\FilterControllerArgumentsEvent;
use WappoVendor\Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use WappoVendor\Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use WappoVendor\Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use WappoVendor\Symfony\Component\HttpKernel\Event\GetResponseEvent;
use WappoVendor\Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use WappoVendor\Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use WappoVendor\Symfony\Component\HttpKernel\Event\PostResponseEvent;
use WappoVendor\Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use WappoVendor\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use WappoVendor\Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
/**
 * HttpKernel notifies events to convert a Request object to a Response one.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class HttpKernel implements \WappoVendor\Symfony\Component\HttpKernel\HttpKernelInterface, \WappoVendor\Symfony\Component\HttpKernel\TerminableInterface
{
    protected $dispatcher;
    protected $resolver;
    protected $requestStack;
    private $argumentResolver;
    public function __construct(\WappoVendor\Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher, \WappoVendor\Symfony\Component\HttpKernel\Controller\ControllerResolverInterface $resolver, \WappoVendor\Symfony\Component\HttpFoundation\RequestStack $requestStack = null, \WappoVendor\Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface $argumentResolver = null)
    {
        $this->dispatcher = $dispatcher;
        $this->resolver = $resolver;
        $this->requestStack = $requestStack ?: new \WappoVendor\Symfony\Component\HttpFoundation\RequestStack();
        $this->argumentResolver = $argumentResolver;
        if (null === $this->argumentResolver) {
            @\trigger_error(\sprintf('As of 3.1 an %s is used to resolve arguments. In 4.0 the $argumentResolver becomes the %s if no other is provided instead of using the $resolver argument.', \WappoVendor\Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface::class, \WappoVendor\Symfony\Component\HttpKernel\Controller\ArgumentResolver::class), \E_USER_DEPRECATED);
            // fallback in case of deprecations
            $this->argumentResolver = $resolver;
        }
    }
    /**
     * {@inheritdoc}
     */
    public function handle(\WappoVendor\Symfony\Component\HttpFoundation\Request $request, $type = \WappoVendor\Symfony\Component\HttpKernel\HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        $request->headers->set('X-Php-Ob-Level', (string) \ob_get_level());
        try {
            return $this->handleRaw($request, $type);
        } catch (\Exception $e) {
            if ($e instanceof \WappoVendor\Symfony\Component\HttpFoundation\Exception\RequestExceptionInterface) {
                $e = new \WappoVendor\Symfony\Component\HttpKernel\Exception\BadRequestHttpException($e->getMessage(), $e);
            }
            if (false === $catch) {
                $this->finishRequest($request, $type);
                throw $e;
            }
            return $this->handleException($e, $request, $type);
        }
    }
    /**
     * {@inheritdoc}
     */
    public function terminate(\WappoVendor\Symfony\Component\HttpFoundation\Request $request, \WappoVendor\Symfony\Component\HttpFoundation\Response $response)
    {
        $this->dispatcher->dispatch(\WappoVendor\Symfony\Component\HttpKernel\KernelEvents::TERMINATE, new \WappoVendor\Symfony\Component\HttpKernel\Event\PostResponseEvent($this, $request, $response));
    }
    /**
     * @internal
     */
    public function terminateWithException(\Exception $exception, \WappoVendor\Symfony\Component\HttpFoundation\Request $request = null)
    {
        if (!($request = $request ?: $this->requestStack->getMasterRequest())) {
            throw $exception;
        }
        $response = $this->handleException($exception, $request, self::MASTER_REQUEST);
        $response->sendHeaders();
        $response->sendContent();
        $this->terminate($request, $response);
    }
    /**
     * Handles a request to convert it to a response.
     *
     * Exceptions are not caught.
     *
     * @param Request $request A Request instance
     * @param int     $type    The type of the request (one of HttpKernelInterface::MASTER_REQUEST or HttpKernelInterface::SUB_REQUEST)
     *
     * @return Response A Response instance
     *
     * @throws \LogicException       If one of the listener does not behave as expected
     * @throws NotFoundHttpException When controller cannot be found
     */
    private function handleRaw(\WappoVendor\Symfony\Component\HttpFoundation\Request $request, $type = self::MASTER_REQUEST)
    {
        $this->requestStack->push($request);
        // request
        $event = new \WappoVendor\Symfony\Component\HttpKernel\Event\GetResponseEvent($this, $request, $type);
        $this->dispatcher->dispatch(\WappoVendor\Symfony\Component\HttpKernel\KernelEvents::REQUEST, $event);
        if ($event->hasResponse()) {
            return $this->filterResponse($event->getResponse(), $request, $type);
        }
        // load controller
        if (false === ($controller = $this->resolver->getController($request))) {
            throw new \WappoVendor\Symfony\Component\HttpKernel\Exception\NotFoundHttpException(\sprintf('Unable to find the controller for path "%s". The route is wrongly configured.', $request->getPathInfo()));
        }
        $event = new \WappoVendor\Symfony\Component\HttpKernel\Event\FilterControllerEvent($this, $controller, $request, $type);
        $this->dispatcher->dispatch(\WappoVendor\Symfony\Component\HttpKernel\KernelEvents::CONTROLLER, $event);
        $controller = $event->getController();
        // controller arguments
        $arguments = $this->argumentResolver->getArguments($request, $controller);
        $event = new \WappoVendor\Symfony\Component\HttpKernel\Event\FilterControllerArgumentsEvent($this, $controller, $arguments, $request, $type);
        $this->dispatcher->dispatch(\WappoVendor\Symfony\Component\HttpKernel\KernelEvents::CONTROLLER_ARGUMENTS, $event);
        $controller = $event->getController();
        $arguments = $event->getArguments();
        // call controller
        $response = \call_user_func_array($controller, $arguments);
        // view
        if (!$response instanceof \WappoVendor\Symfony\Component\HttpFoundation\Response) {
            $event = new \WappoVendor\Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent($this, $request, $type, $response);
            $this->dispatcher->dispatch(\WappoVendor\Symfony\Component\HttpKernel\KernelEvents::VIEW, $event);
            if ($event->hasResponse()) {
                $response = $event->getResponse();
            }
            if (!$response instanceof \WappoVendor\Symfony\Component\HttpFoundation\Response) {
                $msg = \sprintf('The controller must return a response (%s given).', $this->varToString($response));
                // the user may have forgotten to return something
                if (null === $response) {
                    $msg .= ' Did you forget to add a return statement somewhere in your controller?';
                }
                throw new \LogicException($msg);
            }
        }
        return $this->filterResponse($response, $request, $type);
    }
    /**
     * Filters a response object.
     *
     * @param Response $response A Response instance
     * @param Request  $request  An error message in case the response is not a Response object
     * @param int      $type     The type of the request (one of HttpKernelInterface::MASTER_REQUEST or HttpKernelInterface::SUB_REQUEST)
     *
     * @return Response The filtered Response instance
     *
     * @throws \RuntimeException if the passed object is not a Response instance
     */
    private function filterResponse(\WappoVendor\Symfony\Component\HttpFoundation\Response $response, \WappoVendor\Symfony\Component\HttpFoundation\Request $request, $type)
    {
        $event = new \WappoVendor\Symfony\Component\HttpKernel\Event\FilterResponseEvent($this, $request, $type, $response);
        $this->dispatcher->dispatch(\WappoVendor\Symfony\Component\HttpKernel\KernelEvents::RESPONSE, $event);
        $this->finishRequest($request, $type);
        return $event->getResponse();
    }
    /**
     * Publishes the finish request event, then pop the request from the stack.
     *
     * Note that the order of the operations is important here, otherwise
     * operations such as {@link RequestStack::getParentRequest()} can lead to
     * weird results.
     *
     * @param int $type
     */
    private function finishRequest(\WappoVendor\Symfony\Component\HttpFoundation\Request $request, $type)
    {
        $this->dispatcher->dispatch(\WappoVendor\Symfony\Component\HttpKernel\KernelEvents::FINISH_REQUEST, new \WappoVendor\Symfony\Component\HttpKernel\Event\FinishRequestEvent($this, $request, $type));
        $this->requestStack->pop();
    }
    /**
     * Handles an exception by trying to convert it to a Response.
     *
     * @param \Exception $e       An \Exception instance
     * @param Request    $request A Request instance
     * @param int        $type    The type of the request
     *
     * @return Response A Response instance
     *
     * @throws \Exception
     */
    private function handleException(\Exception $e, $request, $type)
    {
        $event = new \WappoVendor\Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent($this, $request, $type, $e);
        $this->dispatcher->dispatch(\WappoVendor\Symfony\Component\HttpKernel\KernelEvents::EXCEPTION, $event);
        // a listener might have replaced the exception
        $e = $event->getException();
        if (!$event->hasResponse()) {
            $this->finishRequest($request, $type);
            throw $e;
        }
        $response = $event->getResponse();
        // the developer asked for a specific status code
        if ($response->headers->has('X-Status-Code')) {
            @\trigger_error(\sprintf('Using the X-Status-Code header is deprecated since Symfony 3.3 and will be removed in 4.0. Use %s::allowCustomResponseCode() instead.', \WappoVendor\Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent::class), \E_USER_DEPRECATED);
            $response->setStatusCode($response->headers->get('X-Status-Code'));
            $response->headers->remove('X-Status-Code');
        } elseif (!$event->isAllowingCustomResponseCode() && !$response->isClientError() && !$response->isServerError() && !$response->isRedirect()) {
            // ensure that we actually have an error response
            if ($e instanceof \WappoVendor\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                // keep the HTTP status code and headers
                $response->setStatusCode($e->getStatusCode());
                $response->headers->add($e->getHeaders());
            } else {
                $response->setStatusCode(500);
            }
        }
        try {
            return $this->filterResponse($response, $request, $type);
        } catch (\Exception $e) {
            return $response;
        }
    }
    /**
     * Returns a human-readable string for the specified variable.
     */
    private function varToString($var)
    {
        if (\is_object($var)) {
            return \sprintf('Object(%s)', \get_class($var));
        }
        if (\is_array($var)) {
            $a = [];
            foreach ($var as $k => $v) {
                $a[] = \sprintf('%s => %s', $k, $this->varToString($v));
            }
            return \sprintf('Array(%s)', \implode(', ', $a));
        }
        if (\is_resource($var)) {
            return \sprintf('Resource(%s)', \get_resource_type($var));
        }
        if (null === $var) {
            return 'null';
        }
        if (false === $var) {
            return 'false';
        }
        if (true === $var) {
            return 'true';
        }
        return (string) $var;
    }
}
