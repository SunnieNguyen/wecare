<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WappoVendor\Symfony\Component\HttpKernel\Controller\ArgumentResolver;

use WappoVendor\Symfony\Component\HttpFoundation\Request;
use WappoVendor\Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use WappoVendor\Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
/**
 * Yields the default value defined in the action signature when no value has been given.
 *
 * @author Iltar van der Berg <kjarli@gmail.com>
 */
final class DefaultValueResolver implements \WappoVendor\Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports(\WappoVendor\Symfony\Component\HttpFoundation\Request $request, \WappoVendor\Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata $argument)
    {
        return $argument->hasDefaultValue() || null !== $argument->getType() && $argument->isNullable() && !$argument->isVariadic();
    }
    /**
     * {@inheritdoc}
     */
    public function resolve(\WappoVendor\Symfony\Component\HttpFoundation\Request $request, \WappoVendor\Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata $argument)
    {
        (yield $argument->hasDefaultValue() ? $argument->getDefaultValue() : null);
    }
}
