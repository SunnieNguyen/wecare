<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WappoVendor\Symfony\Component\HttpKernel\Event;

use WappoVendor\Symfony\Component\HttpFoundation\Request;
use WappoVendor\Symfony\Component\HttpFoundation\Response;
use WappoVendor\Symfony\Component\HttpKernel\HttpKernelInterface;
/**
 * Allows to execute logic after a response was sent.
 *
 * Since it's only triggered on master requests, the `getRequestType()` method
 * will always return the value of `HttpKernelInterface::MASTER_REQUEST`.
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class PostResponseEvent extends \WappoVendor\Symfony\Component\HttpKernel\Event\KernelEvent
{
    private $response;
    public function __construct(\WappoVendor\Symfony\Component\HttpKernel\HttpKernelInterface $kernel, \WappoVendor\Symfony\Component\HttpFoundation\Request $request, \WappoVendor\Symfony\Component\HttpFoundation\Response $response)
    {
        parent::__construct($kernel, $request, \WappoVendor\Symfony\Component\HttpKernel\HttpKernelInterface::MASTER_REQUEST);
        $this->response = $response;
    }
    /**
     * Returns the response for which this event was thrown.
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
