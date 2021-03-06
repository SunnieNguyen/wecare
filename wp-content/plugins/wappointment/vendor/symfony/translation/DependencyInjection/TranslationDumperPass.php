<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WappoVendor\Symfony\Component\Translation\DependencyInjection;

use WappoVendor\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use WappoVendor\Symfony\Component\DependencyInjection\ContainerBuilder;
use WappoVendor\Symfony\Component\DependencyInjection\Reference;
/**
 * Adds tagged translation.formatter services to translation writer.
 */
class TranslationDumperPass implements \WappoVendor\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface
{
    private $writerServiceId;
    private $dumperTag;
    public function __construct($writerServiceId = 'translation.writer', $dumperTag = 'translation.dumper')
    {
        $this->writerServiceId = $writerServiceId;
        $this->dumperTag = $dumperTag;
    }
    public function process(\WappoVendor\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        if (!$container->hasDefinition($this->writerServiceId)) {
            return;
        }
        $definition = $container->getDefinition($this->writerServiceId);
        foreach ($container->findTaggedServiceIds($this->dumperTag, true) as $id => $attributes) {
            $definition->addMethodCall('addDumper', [$attributes[0]['alias'], new \WappoVendor\Symfony\Component\DependencyInjection\Reference($id)]);
        }
    }
}
