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

use WappoVendor\Symfony\Component\Console\ConsoleEvents;
use WappoVendor\Symfony\Component\EventDispatcher\EventSubscriberInterface;
use WappoVendor\Symfony\Component\VarDumper\Cloner\ClonerInterface;
use WappoVendor\Symfony\Component\VarDumper\Dumper\DataDumperInterface;
use WappoVendor\Symfony\Component\VarDumper\VarDumper;
/**
 * Configures dump() handler.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class DumpListener implements \WappoVendor\Symfony\Component\EventDispatcher\EventSubscriberInterface
{
    private $cloner;
    private $dumper;
    public function __construct(\WappoVendor\Symfony\Component\VarDumper\Cloner\ClonerInterface $cloner, \WappoVendor\Symfony\Component\VarDumper\Dumper\DataDumperInterface $dumper)
    {
        $this->cloner = $cloner;
        $this->dumper = $dumper;
    }
    public function configure()
    {
        $cloner = $this->cloner;
        $dumper = $this->dumper;
        \WappoVendor\Symfony\Component\VarDumper\VarDumper::setHandler(function ($var) use($cloner, $dumper) {
            $dumper->dump($cloner->cloneVar($var));
        });
    }
    public static function getSubscribedEvents()
    {
        if (!\class_exists(\WappoVendor\Symfony\Component\Console\ConsoleEvents::class)) {
            return [];
        }
        // Register early to have a working dump() as early as possible
        return [\WappoVendor\Symfony\Component\Console\ConsoleEvents::COMMAND => ['configure', 1024]];
    }
}
