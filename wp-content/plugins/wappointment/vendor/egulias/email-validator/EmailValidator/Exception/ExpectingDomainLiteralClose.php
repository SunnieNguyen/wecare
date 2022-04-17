<?php

namespace WappoVendor\Egulias\EmailValidator\Exception;

class ExpectingDomainLiteralClose extends \WappoVendor\Egulias\EmailValidator\Exception\InvalidEmail
{
    const CODE = 137;
    const REASON = "Closing bracket ']' for domain literal not found";
}
