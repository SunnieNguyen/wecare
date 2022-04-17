<?php

namespace WappoVendor\Illuminate\Support\Facades;

/**
 * @see \Illuminate\Routing\Redirector
 */
class Redirect extends \WappoVendor\Illuminate\Support\Facades\Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'redirect';
    }
}
