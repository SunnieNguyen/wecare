<?php

namespace WappoVendor\Rakit\Validation\Rules;

use WappoVendor\Rakit\Validation\Rule;
class AlphaDash extends \WappoVendor\Rakit\Validation\Rule
{
    protected $message = "The :attribute only allows a-z, 0-9, _ and -";
    public function check($value)
    {
        if (!\is_string($value) && !\is_numeric($value)) {
            return false;
        }
        return \preg_match('/^[\\pL\\pM\\pN_-]+$/u', $value) > 0;
    }
}
