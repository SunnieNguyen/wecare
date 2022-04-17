<?php

namespace WappoVendor\Egulias\EmailValidator\Warning;

use WappoVendor\Egulias\EmailValidator\EmailParser;
class EmailTooLong extends \WappoVendor\Egulias\EmailValidator\Warning\Warning
{
    const CODE = 66;
    public function __construct()
    {
        $this->message = 'Email is too long, exceeds ' . \WappoVendor\Egulias\EmailValidator\EmailParser::EMAIL_MAX_LENGTH;
    }
}
