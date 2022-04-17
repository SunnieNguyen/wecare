<?php

namespace WappoVendor\Illuminate\Contracts\Validation;

use WappoVendor\Illuminate\Contracts\Support\MessageProvider;
interface Validator extends \WappoVendor\Illuminate\Contracts\Support\MessageProvider
{
    /**
     * Determine if the data fails the validation rules.
     *
     * @return bool
     */
    public function fails();
    /**
     * Get the failed validation rules.
     *
     * @return array
     */
    public function failed();
    /**
     * Add conditions to a given field based on a Closure.
     *
     * @param  string  $attribute
     * @param  string|array  $rules
     * @param  callable  $callback
     * @return $this
     */
    public function sometimes($attribute, $rules, callable $callback);
    /**
     * After an after validation callback.
     *
     * @param  callable|string  $callback
     * @return $this
     */
    public function after($callback);
    /**
     * Get all of the validation error messages.
     *
     * @return \Illuminate\Support\MessageBag
     */
    public function errors();
}
