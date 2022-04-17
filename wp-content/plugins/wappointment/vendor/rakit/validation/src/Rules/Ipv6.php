<?php

namespace WappoVendor\Rakit\Validation\Rules;

use WappoVendor\Rakit\Validation\Rule;
class Ipv6 extends \WappoVendor\Rakit\Validation\Rule
{
    protected $message = "The :attribute is not valid IPv6 Address";
    public function check($value)
    {
        return \filter_var($value, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV6) !== false;
    }
}
