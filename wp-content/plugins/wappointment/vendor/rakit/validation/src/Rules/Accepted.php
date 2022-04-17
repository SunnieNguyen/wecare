<?php

namespace WappoVendor\Rakit\Validation\Rules;

use WappoVendor\Rakit\Validation\Rule;
class Accepted extends \WappoVendor\Rakit\Validation\Rule
{
    protected $implicit = true;
    protected $message = "The :attribute must be accepted";
    public function check($value)
    {
        $acceptables = ['yes', 'on', '1', 1, true, 'true'];
        return \in_array($value, $acceptables, true);
    }
}
