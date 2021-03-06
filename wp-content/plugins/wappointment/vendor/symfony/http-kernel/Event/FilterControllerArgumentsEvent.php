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
use WappoVendor\Symfony\Component\HttpKernel\HttpKernelInterface;
/**
 * Allows filtering of controller arguments.
 *
 * You can call getController() to retrieve the controller and getArguments
 * to retrieve the current arguments. With setArguments() you can replace
 * arguments that are used to call the controller.
 *
 * Arguments set in the event must be compatible with the signature of the
 * controller.
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class FilterControllerArgumentsEvent extends \WappoVendor\Symfony\Component\HttpKernel\Event\FilterControllerEvent
{
    private $arguments;
    public function __construct(\WappoVendor\Symfony\Component\HttpKernel\HttpKernelInterface $kernel, callable $controller, array $arguments, \WappoVendor\Symfony\Component\HttpFoundation\Request $request, $requestType)
    {
        parent::__construct($kernel, $controller, $request, $requestType);
        $this->arguments = $arguments;
    }
    /**
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }
    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;
    }
}
