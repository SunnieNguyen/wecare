<?php

namespace WappoVendor\Illuminate\Support\Facades;

use WappoVendor\Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;
/**
 * @method static int handle(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output = null)
 * @method static int call(string $command, array $parameters = [], $outputBuffer = null)
 * @method static int queue(string $command, array $parameters = [])
 * @method static array all()
 * @method static string output()
 *
 * @see \Illuminate\Contracts\Console\Kernel
 */
class Artisan extends \WappoVendor\Illuminate\Support\Facades\Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \WappoVendor\Illuminate\Contracts\Console\Kernel::class;
    }
}
