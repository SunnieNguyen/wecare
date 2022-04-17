<?php

namespace WappoVendor\Egulias\EmailValidator\Exception;

class ConsecutiveAt extends \WappoVendor\Egulias\EmailValidator\Exception\InvalidEmail
{
    const CODE = 128;
    const REASON = "Consecutive AT";
}
