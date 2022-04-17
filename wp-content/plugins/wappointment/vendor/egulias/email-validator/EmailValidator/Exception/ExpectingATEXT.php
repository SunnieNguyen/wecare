<?php

namespace WappoVendor\Egulias\EmailValidator\Exception;

class ExpectingATEXT extends \WappoVendor\Egulias\EmailValidator\Exception\InvalidEmail
{
    const CODE = 137;
    const REASON = "Expecting ATEXT";
}
