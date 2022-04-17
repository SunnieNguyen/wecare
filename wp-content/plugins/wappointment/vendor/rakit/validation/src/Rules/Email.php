<?php

namespace WappoVendor\Rakit\Validation\Rules;

use WappoVendor\Rakit\Validation\Rule;
class Email extends \WappoVendor\Rakit\Validation\Rule
{
    protected $message = "The :attribute is not valid email";
    public function check($value)
    {
        return \filter_var($value, \FILTER_VALIDATE_EMAIL) !== false;
    }
}
