<?php

namespace WappoVendor\Rakit\Validation\Rules;

use WappoVendor\Rakit\Validation\Rule;
class Date extends \WappoVendor\Rakit\Validation\Rule
{
    protected $message = "The :attribute is not valid date format";
    protected $fillable_params = ['format'];
    protected $params = ['format' => 'Y-m-d'];
    public function check($value)
    {
        $this->requireParameters($this->fillable_params);
        $format = $this->parameter('format');
        return \date_create_from_format($format, $value) !== false;
    }
}
