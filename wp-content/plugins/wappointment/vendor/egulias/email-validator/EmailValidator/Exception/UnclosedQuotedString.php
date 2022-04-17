<?php

namespace WappoVendor\Egulias\EmailValidator\Exception;

class UnclosedQuotedString extends \WappoVendor\Egulias\EmailValidator\Exception\InvalidEmail
{
    const CODE = 145;
    const REASON = "Unclosed quoted string";
}
