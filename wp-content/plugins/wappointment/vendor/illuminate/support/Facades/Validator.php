<?php

namespace WappoVendor\Illuminate\Support\Facades;

/**
 * @see \Illuminate\Validation\Factory
 */
class Validator extends \WappoVendor\Illuminate\Support\Facades\Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'validator';
    }
}
