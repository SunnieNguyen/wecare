<?php

namespace WappoVendor\Illuminate\Support\Facades;

use WappoVendor\Illuminate\Contracts\Routing\ResponseFactory as ResponseFactoryContract;
/**
 * @see \Illuminate\Contracts\Routing\ResponseFactory
 */
class Response extends \WappoVendor\Illuminate\Support\Facades\Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \WappoVendor\Illuminate\Contracts\Routing\ResponseFactory::class;
    }
}
