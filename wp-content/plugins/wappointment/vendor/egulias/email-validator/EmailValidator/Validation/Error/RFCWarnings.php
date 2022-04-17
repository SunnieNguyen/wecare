<?php

namespace WappoVendor\Egulias\EmailValidator\Validation\Error;

use WappoVendor\Egulias\EmailValidator\Exception\InvalidEmail;
class RFCWarnings extends \WappoVendor\Egulias\EmailValidator\Exception\InvalidEmail
{
    const CODE = 997;
    const REASON = 'Warnings were found.';
}
