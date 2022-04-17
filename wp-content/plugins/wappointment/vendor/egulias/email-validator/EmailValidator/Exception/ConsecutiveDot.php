<?php

namespace WappoVendor\Egulias\EmailValidator\Exception;

class ConsecutiveDot extends \WappoVendor\Egulias\EmailValidator\Exception\InvalidEmail
{
    const CODE = 132;
    const REASON = "Consecutive DOT";
}
