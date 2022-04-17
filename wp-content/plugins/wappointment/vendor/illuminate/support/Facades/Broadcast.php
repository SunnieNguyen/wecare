<?php

namespace WappoVendor\Illuminate\Support\Facades;

use WappoVendor\Illuminate\Contracts\Broadcasting\Factory as BroadcastingFactoryContract;
/**
 * @see \Illuminate\Contracts\Broadcasting\Factory
 */
class Broadcast extends \WappoVendor\Illuminate\Support\Facades\Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \WappoVendor\Illuminate\Contracts\Broadcasting\Factory::class;
    }
}
