<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WappoVendor\Symfony\Component\HttpKernel\DataCollector;

use WappoVendor\Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcherInterface;
use WappoVendor\Symfony\Component\EventDispatcher\EventDispatcherInterface;
use WappoVendor\Symfony\Component\HttpFoundation\Request;
use WappoVendor\Symfony\Component\HttpFoundation\Response;
/**
 * EventDataCollector.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class EventDataCollector extends \WappoVendor\Symfony\Component\HttpKernel\DataCollector\DataCollector implements \WappoVendor\Symfony\Component\HttpKernel\DataCollector\LateDataCollectorInterface
{
    protected $dispatcher;
    public function __construct(\WappoVendor\Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher = null)
    {
        if ($dispatcher instanceof \WappoVendor\Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcherInterface && !\method_exists($dispatcher, 'reset')) {
            @\trigger_error(\sprintf('Implementing "%s" without the "reset()" method is deprecated since Symfony 3.4 and will be unsupported in 4.0 for class "%s".', \WappoVendor\Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcherInterface::class, \get_class($dispatcher)), \E_USER_DEPRECATED);
        }
        $this->dispatcher = $dispatcher;
    }
    /**
     * {@inheritdoc}
     */
    public function collect(\WappoVendor\Symfony\Component\HttpFoundation\Request $request, \WappoVendor\Symfony\Component\HttpFoundation\Response $response, \Exception $exception = null)
    {
        $this->data = ['called_listeners' => [], 'not_called_listeners' => []];
    }
    public function reset()
    {
        $this->data = [];
        if ($this->dispatcher instanceof \WappoVendor\Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcherInterface) {
            if (!\method_exists($this->dispatcher, 'reset')) {
                return;
                // @deprecated
            }
            $this->dispatcher->reset();
        }
    }
    public function lateCollect()
    {
        if ($this->dispatcher instanceof \WappoVendor\Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcherInterface) {
            $this->setCalledListeners($this->dispatcher->getCalledListeners());
            $this->setNotCalledListeners($this->dispatcher->getNotCalledListeners());
        }
        $this->data = $this->cloneVar($this->data);
    }
    /**
     * Sets the called listeners.
     *
     * @param array $listeners An array of called listeners
     *
     * @see TraceableEventDispatcherInterface
     */
    public function setCalledListeners(array $listeners)
    {
        $this->data['called_listeners'] = $listeners;
    }
    /**
     * Gets the called listeners.
     *
     * @return array An array of called listeners
     *
     * @see TraceableEventDispatcherInterface
     */
    public function getCalledListeners()
    {
        return $this->data['called_listeners'];
    }
    /**
     * Sets the not called listeners.
     *
     * @param array $listeners An array of not called listeners
     *
     * @see TraceableEventDispatcherInterface
     */
    public function setNotCalledListeners(array $listeners)
    {
        $this->data['not_called_listeners'] = $listeners;
    }
    /**
     * Gets the not called listeners.
     *
     * @return array An array of not called listeners
     *
     * @see TraceableEventDispatcherInterface
     */
    public function getNotCalledListeners()
    {
        return $this->data['not_called_listeners'];
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'events';
    }
}
