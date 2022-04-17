<?php

namespace WappoVendor\Illuminate\Support\Facades;

use WappoVendor\Illuminate\Support\Testing\Fakes\MailFake;
/**
 * @see \Illuminate\Mail\Mailer
 */
class Mail extends \WappoVendor\Illuminate\Support\Facades\Facade
{
    /**
     * Replace the bound instance with a fake.
     *
     * @return void
     */
    public static function fake()
    {
        static::swap(new \WappoVendor\Illuminate\Support\Testing\Fakes\MailFake());
    }
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'mailer';
    }
}
