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

use WappoVendor\Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use WappoVendor\Symfony\Component\DependencyInjection\ChildDefinition;
use WappoVendor\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use WappoVendor\Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use WappoVendor\Symfony\Component\DependencyInjection\ContainerAwareInterface;
use WappoVendor\Symfony\Component\DependencyInjection\ContainerBuilder;
use WappoVendor\Symfony\Component\DependencyInjection\ContainerInterface;
use WappoVendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use WappoVendor\Symfony\Component\DependencyInjection\LazyProxy\ProxyHelper;
use WappoVendor\Symfony\Component\DependencyInjection\Reference;
use WappoVendor\Symfony\Component\DependencyInjection\TypedReference;
use WappoVendor\Symfony\Component\HttpFoundation\Request;
/**
 * Creates the service-locators required by ServiceValueResolver.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class RegisterControllerArgumentLocatorsPass implements \WappoVendor\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface
{
    private $resolverServiceId;
    private $controllerTag;
    public function __construct($resolverServiceId = 'argument_resolver.service', $controllerTag = 'controller.service_arguments')
    {
        $this->resolverServiceId = $resolverServiceId;
        $this->controllerTag = $controllerTag;
    }
    public function process(\WappoVendor\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        if (false === $container->hasDefinition($this->resolverServiceId)) {
            return;
        }
        $parameterBag = $container->getParameterBag();
        $controllers = [];
        foreach ($container->findTaggedServiceIds($this->controllerTag, true) as $id => $tags) {
            $def = $container->getDefinition($id);
            $def->setPublic(true);
            $class = $def->getClass();
            $autowire = $def->isAutowired();
            $bindings = $def->getBindings();
            // resolve service class, taking parent definitions into account
            while ($def instanceof \WappoVendor\Symfony\Component\DependencyInjection\ChildDefinition) {
                $def = $container->findDefinition($def->getParent());
                $class = $class ?: $def->getClass();
                $bindings += $def->getBindings();
            }
            $class = $parameterBag->resolveValue($class);
            if (!($r = $container->getReflectionClass($class))) {
                throw new \WappoVendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Class "%s" used for service "%s" cannot be found.', $class, $id));
            }
            $isContainerAware = $r->implementsInterface(\WappoVendor\Symfony\Component\DependencyInjection\ContainerAwareInterface::class) || \is_subclass_of($class, \WappoVendor\Symfony\Bundle\FrameworkBundle\Controller\AbstractController::class);
            // get regular public methods
            $methods = [];
            $arguments = [];
            foreach ($r->getMethods(\ReflectionMethod::IS_PUBLIC) as $r) {
                if ('setContainer' === $r->name && $isContainerAware) {
                    continue;
                }
                if (!$r->isConstructor() && !$r->isDestructor() && !$r->isAbstract()) {
                    $methods[\strtolower($r->name)] = [$r, $r->getParameters()];
                }
            }
            // validate and collect explicit per-actions and per-arguments service references
            foreach ($tags as $attributes) {
                if (!isset($attributes['action']) && !isset($attributes['argument']) && !isset($attributes['id'])) {
                    $autowire = true;
                    continue;
                }
                foreach (['action', 'argument', 'id'] as $k) {
                    if (!isset($attributes[$k][0])) {
                        throw new \WappoVendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Missing "%s" attribute on tag "%s" %s for service "%s".', $k, $this->controllerTag, \json_encode($attributes, \JSON_UNESCAPED_UNICODE), $id));
                    }
                }
                if (!isset($methods[$action = \strtolower($attributes['action'])])) {
                    throw new \WappoVendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Invalid "action" attribute on tag "%s" for service "%s": no public "%s()" method found on class "%s".', $this->controllerTag, $id, $attributes['action'], $class));
                }
                list($r, $parameters) = $methods[$action];
                $found = false;
                foreach ($parameters as $p) {
                    if ($attributes['argument'] === $p->name) {
                        if (!isset($arguments[$r->name][$p->name])) {
                            $arguments[$r->name][$p->name] = $attributes['id'];
                        }
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    throw new \WappoVendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Invalid "%s" tag for service "%s": method "%s()" has no "%s" argument on class "%s".', $this->controllerTag, $id, $r->name, $attributes['argument'], $class));
                }
            }
            foreach ($methods as list($r, $parameters)) {
                /** @var \ReflectionMethod $r */
                // create a per-method map of argument-names to service/type-references
                $args = [];
                foreach ($parameters as $p) {
                    /** @var \ReflectionParameter $p */
                    $type = $target = \WappoVendor\Symfony\Component\DependencyInjection\LazyProxy\ProxyHelper::getTypeHint($r, $p, true);
                    $invalidBehavior = \WappoVendor\Symfony\Component\DependencyInjection\ContainerInterface::IGNORE_ON_INVALID_REFERENCE;
                    if (isset($arguments[$r->name][$p->name])) {
                        $target = $arguments[$r->name][$p->name];
                        if ('?' !== $target[0]) {
                            $invalidBehavior = \WappoVendor\Symfony\Component\DependencyInjection\ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE;
                        } elseif ('' === ($target = (string) \substr($target, 1))) {
                            throw new \WappoVendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('A "%s" tag must have non-empty "id" attributes for service "%s".', $this->controllerTag, $id));
                        } elseif ($p->allowsNull() && !$p->isOptional()) {
                            $invalidBehavior = \WappoVendor\Symfony\Component\DependencyInjection\ContainerInterface::NULL_ON_INVALID_REFERENCE;
                        }
                    } elseif (isset($bindings[$bindingName = '$' . $p->name]) || isset($bindings[$bindingName = $type])) {
                        $binding = $bindings[$bindingName];
                        list($bindingValue, $bindingId) = $binding->getValues();
                        if (!$bindingValue instanceof \WappoVendor\Symfony\Component\DependencyInjection\Reference) {
                            continue;
                        }
                        $binding->setValues([$bindingValue, $bindingId, true]);
                        $args[$p->name] = $bindingValue;
                        continue;
                    } elseif (!$type || !$autowire) {
                        continue;
                    }
                    if (\WappoVendor\Symfony\Component\HttpFoundation\Request::class === $type) {
                        continue;
                    }
                    if ($type && !$p->isOptional() && !$p->allowsNull() && !\class_exists($type) && !\interface_exists($type, false)) {
                        $message = \sprintf('Cannot determine controller argument for "%s::%s()": the $%s argument is type-hinted with the non-existent class or interface: "%s".', $class, $r->name, $p->name, $type);
                        // see if the type-hint lives in the same namespace as the controller
                        if (0 === \strncmp($type, $class, \strrpos($class, '\\'))) {
                            $message .= ' Did you forget to add a use statement?';
                        }
                        throw new \WappoVendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException($message);
                    }
                    $args[$p->name] = $type ? new \WappoVendor\Symfony\Component\DependencyInjection\TypedReference($target, $type, $r->class, $invalidBehavior) : new \WappoVendor\Symfony\Component\DependencyInjection\Reference($target, $invalidBehavior);
                }
                // register the maps as a per-method service-locators
                if ($args) {
                    $controllers[$id . ':' . $r->name] = \WappoVendor\Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass::register($container, $args);
                }
            }
        }
        $container->getDefinition($this->resolverServiceId)->replaceArgument(0, \WappoVendor\Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass::register($container, $controllers));
    }
}
