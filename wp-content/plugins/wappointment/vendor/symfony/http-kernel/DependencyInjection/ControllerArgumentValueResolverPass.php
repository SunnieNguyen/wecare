<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WappoVendor\Symfony\Component\HttpKernel\DependencyInjection;

use WappoVendor\Symfony\Component\DependencyInjection\Argument\IteratorArgument;
use WappoVendor\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use WappoVendor\Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use WappoVendor\Symfony\Component\DependencyInjection\ContainerBuilder;
/**
 * Gathers and configures the argument value resolvers.
 *
 * @author Iltar van der Berg <kjarli@gmail.com>
 */
class ControllerArgumentValueResolverPass implements \WappoVendor\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface
{
    use PriorityTaggedServiceTrait;
    private $argumentResolverService;
    private $argumentValueResolverTag;
    public function __construct($argumentResolverService = 'argument_resolver', $argumentValueResolverTag = 'controller.argument_value_resolver')
    {
        $this->argumentResolverService = $argumentResolverService;
        $this->argumentValueResolverTag = $argumentValueResolverTag;
    }
    public function process(\WappoVendor\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        if (!$container->hasDefinition($this->argumentResolverService)) {
            return;
        }
        $container->getDefinition($this->argumentResolverService)->replaceArgument(1, new \WappoVendor\Symfony\Component\DependencyInjection\Argument\IteratorArgument($this->findAndSortTaggedServices($this->argumentValueResolverTag, $container)));
    }
}
