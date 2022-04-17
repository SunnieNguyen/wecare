<?php

namespace WappoVendor\Rakit\Validation\Rules;

use WappoVendor\Rakit\Validation\Rule;
class Present extends \WappoVendor\Rakit\Validation\Rule
{
    protected $implicit = true;
    protected $message = "The :attribute must be present";
    public function check($value)
    {
        return $this->validation->hasValue($this->attribute->getKey());
    }
}
