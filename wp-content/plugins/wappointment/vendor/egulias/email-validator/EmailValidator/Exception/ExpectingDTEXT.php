<?php

namespace WappoVendor\Egulias\EmailValidator\Exception;

class ExpectingDTEXT extends \WappoVendor\Egulias\EmailValidator\Exception\InvalidEmail
{
    const CODE = 129;
    const REASON = "Expected DTEXT";
}
