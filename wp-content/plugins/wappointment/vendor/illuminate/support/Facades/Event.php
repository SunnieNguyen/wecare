<?php

namespace WappoVendor\Illuminate\Support\Facades;

use WappoVendor\Illuminate\Database\Eloquent\Model;
use WappoVendor\Illuminate\Support\Testing\Fakes\EventFake;
/**
 * @see \Illuminate\Events\Dispatcher
 */
class Event extends \WappoVendor\Illuminate\Support\Facades\Facade
{
    /**
     * Replace the bound instance with a fake.
     *
     * @param  array|string  $eventsToFake
     * @return void
     */
    public static function fake($eventsToFake = [])
    {
        static::swap($fake = new \WappoVendor\Illuminate\Support\Testing\Fakes\EventFake(static::getFacadeRoot(), $eventsToFake));
        \WappoVendor\Illuminate\Database\Eloquent\Model::setEventDispatcher($fake);
    }
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'events';
    }
}
