<?php

namespace WappoVendor\Illuminate\Support\Facades;

use WappoVendor\Illuminate\Support\Testing\Fakes\QueueFake;
/**
 * @see \Illuminate\Queue\QueueManager
 * @see \Illuminate\Queue\Queue
 */
class Queue extends \WappoVendor\Illuminate\Support\Facades\Facade
{
    /**
     * Replace the bound instance with a fake.
     *
     * @return void
     */
    public static function fake()
    {
        static::swap(new \WappoVendor\Illuminate\Support\Testing\Fakes\QueueFake(static::getFacadeApplication()));
    }
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'queue';
    }
}
