<?php

namespace WappoVendor\Rakit\Validation\Rules;

use WappoVendor\Rakit\Validation\Rule;
class Ipv4 extends \WappoVendor\Rakit\Validation\Rule
{
    protected $message = "The :attribute is not valid IPv4 Address";
    public function check($value)
    {
        return \filter_var($value, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV4) !== false;
    }
}
