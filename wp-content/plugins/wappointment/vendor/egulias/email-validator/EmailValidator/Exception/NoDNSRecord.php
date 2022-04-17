<?php

namespace WappoVendor\Egulias\EmailValidator\Exception;

class NoDNSRecord extends \WappoVendor\Egulias\EmailValidator\Exception\InvalidEmail
{
    const CODE = 5;
    const REASON = 'No MX or A DSN record was found for this email';
}
