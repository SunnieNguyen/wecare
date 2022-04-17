<?php

namespace WappoVendor\Illuminate\Support\Facades;

/**
 * @see \Illuminate\Config\Repository
 */
class Config extends \WappoVendor\Illuminate\Support\Facades\Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'config';
    }
}
