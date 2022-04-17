<?php

namespace WappoVendor\Rakit\Validation\Rules;

use WappoVendor\Rakit\Validation\Rule;
class Ip extends \WappoVendor\Rakit\Validation\Rule
{
    protected $message = "The :attribute is not valid IP Address";
    public function check($value)
    {
        return \filter_var($value, \FILTER_VALIDATE_IP) !== false;
    }
}
