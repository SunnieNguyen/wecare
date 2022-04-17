<?php

namespace WappoVendor\Rakit\Validation\Rules;

use WappoVendor\Rakit\Validation\Rule;
class Alpha extends \WappoVendor\Rakit\Validation\Rule
{
    protected $message = "The :attribute only allows alphabet characters";
    public function check($value)
    {
        return \is_string($value) && \preg_match('/^[\\pL\\pM]+$/u', $value);
    }
}
