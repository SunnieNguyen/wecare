<?php

namespace WappoVendor\Rakit\Validation\Rules;

use WappoVendor\Rakit\Validation\Rule;
class Numeric extends \WappoVendor\Rakit\Validation\Rule
{
    protected $message = "The :attribute must be numeric";
    public function check($value)
    {
        return \is_numeric($value);
    }
}
