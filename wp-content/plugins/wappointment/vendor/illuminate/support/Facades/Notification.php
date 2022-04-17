<?php

namespace WappoVendor\Illuminate\Support\Facades;

use WappoVendor\Illuminate\Notifications\ChannelManager;
use WappoVendor\Illuminate\Notifications\AnonymousNotifiable;
use WappoVendor\Illuminate\Support\Testing\Fakes\NotificationFake;
/**
 * @see \Illuminate\Notifications\ChannelManager
 */
class Notification extends \WappoVendor\Illuminate\Support\Facades\Facade
{
    /**
     * Replace the bound instance with a fake.
     *
     * @return \Illuminate\Support\Testing\Fakes\NotificationFake
     */
    public static function fake()
    {
        static::swap($fake = new \WappoVendor\Illuminate\Support\Testing\Fakes\NotificationFake());
        return $fake;
    }
    /**
     * Begin sending a notification to an anonymous notifiable.
     *
     * @param  string  $channel
     * @param  mixed  $route
     * @return \Illuminate\Notifications\AnonymousNotifiable
     */
    public static function route($channel, $route)
    {
        return (new \WappoVendor\Illuminate\Notifications\AnonymousNotifiable())->route($channel, $route);
    }
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \WappoVendor\Illuminate\Notifications\ChannelManager::class;
    }
}
