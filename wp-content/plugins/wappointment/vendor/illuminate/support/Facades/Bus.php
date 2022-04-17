<?php

namespace WappoVendor\Illuminate\Support\Facades;

use WappoVendor\Illuminate\Support\Testing\Fakes\BusFake;
use WappoVendor\Illuminate\Contracts\Bus\Dispatcher as BusDispatcherContract;
/**
 * @see \Illuminate\Contracts\Bus\Dispatcher
 */
class Bus extends \WappoVendor\Illuminate\Support\Facades\Facade
{
    /**
     * Replace the bound instance with a fake.
     *
     * @return void
     */
    public static function fake()
    {
        static::swap(new \WappoVendor\Illuminate\Support\Testing\Fakes\BusFake());
    }
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \WappoVendor\Illuminate\Contracts\Bus\Dispatcher::class;
    }
}
