<?php

namespace WappoVendor\Rakit\Validation\Rules;

use WappoVendor\Rakit\Validation\Rule;
class Same extends \WappoVendor\Rakit\Validation\Rule
{
    protected $message = "The :attribute must be same with :field";
    protected $fillable_params = ['field'];
    public function check($value)
    {
        $this->requireParameters($this->fillable_params);
        $field = $this->parameter('field');
        $anotherValue = $this->getAttribute()->getValue($field);
        return $value == $anotherValue;
    }
}
