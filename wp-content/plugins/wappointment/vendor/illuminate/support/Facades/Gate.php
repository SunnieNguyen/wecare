<?php

namespace WappoVendor\Illuminate\Support\Facades;

use WappoVendor\Illuminate\Contracts\Auth\Access\Gate as GateContract;
/**
 * @see \Illuminate\Contracts\Auth\Access\Gate
 */
class Gate extends \WappoVendor\Illuminate\Support\Facades\Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \WappoVendor\Illuminate\Contracts\Auth\Access\Gate::class;
    }
}
