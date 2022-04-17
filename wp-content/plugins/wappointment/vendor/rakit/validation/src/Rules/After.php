<?php

namespace WappoVendor\Rakit\Validation\Rules;

use WappoVendor\Rakit\Validation\Rule;
class After extends \WappoVendor\Rakit\Validation\Rule
{
    use DateUtils;
    protected $message = "The :attribute must be a date after :time.";
    protected $fillable_params = ['time'];
    public function check($value)
    {
        $this->requireParameters($this->fillable_params);
        $time = $this->parameter('time');
        if (!$this->isValidDate($value)) {
            throw $this->throwException($value);
        }
        if (!$this->isValidDate($time)) {
            throw $this->throwException($time);
        }
        return $this->getTimeStamp($time) < $this->getTimeStamp($value);
    }
}
