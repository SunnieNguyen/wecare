<?php

namespace WappoVendor\Illuminate\Support\Facades;

/**
 * @see \Illuminate\Encryption\Encrypter
 */
class Crypt extends \WappoVendor\Illuminate\Support\Facades\Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'encrypter';
    }
}
