<?php

namespace WappoVendor\Rakit\Validation\Rules;

use WappoVendor\Rakit\Validation\Rule;
class Regex extends \WappoVendor\Rakit\Validation\Rule
{
    protected $message = "The :attribute is not valid format";
    protected $fillable_params = ['regex'];
    public function check($value)
    {
        $this->requireParameters($this->fillable_params);
        $regex = $this->parameter('regex');
        return \preg_match($regex, $value) > 0;
    }
}
