<?php

namespace WappoVendor\Rakit\Validation\Rules;

use WappoVendor\Rakit\Validation\Rule;
class AlphaNum extends \WappoVendor\Rakit\Validation\Rule
{
    protected $message = "The :attribute only allows alphabet and numeric";
    public function check($value)
    {
        if (!\is_string($value) && !\is_numeric($value)) {
            return false;
        }
        return \preg_match('/^[\\pL\\pM\\pN]+$/u', $value) > 0;
    }
}
