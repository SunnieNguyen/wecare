<?php

namespace WappoVendor\Rakit\Validation\Rules;

use WappoVendor\Rakit\Validation\Rule;
class Different extends \WappoVendor\Rakit\Validation\Rule
{
    protected $message = "The :attribute must be different with :field";
    protected $fillable_params = ['field'];
    public function check($value)
    {
        $this->requireParameters($this->fillable_params);
        $field = $this->parameter('field');
        $another_value = $this->validation->getValue($field);
        return $value != $another_value;
    }
}
