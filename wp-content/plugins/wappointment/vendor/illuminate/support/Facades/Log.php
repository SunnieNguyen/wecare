<?php

namespace WappoVendor\Illuminate\Support\Facades;

use WappoVendor\Psr\Log\LoggerInterface;
/**
 * @see \Illuminate\Log\Writer
 */
class Log extends \WappoVendor\Illuminate\Support\Facades\Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \WappoVendor\Psr\Log\LoggerInterface::class;
    }
}
