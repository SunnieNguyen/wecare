<?php

namespace WappoVendor\Rakit\Validation\Rules;

use WappoVendor\Rakit\Validation\Rule;
class Defaults extends \WappoVendor\Rakit\Validation\Rule
{
    protected $message = "The :attribute default is :default";
    protected $fillable_params = ['default'];
    public function check($value)
    {
        $this->requireParameters($this->fillable_params);
        $default = $this->parameter('default');
        return $default;
    }
}
