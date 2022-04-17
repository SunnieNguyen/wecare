<?php

namespace WappoVendor\Rakit\Validation\Rules;

use WappoVendor\Rakit\Validation\Rule;
class TypeArray extends \WappoVendor\Rakit\Validation\Rule
{
    protected $message = "The :attribute must be array";
    public function check($value)
    {
        return \is_array($value);
    }
}
