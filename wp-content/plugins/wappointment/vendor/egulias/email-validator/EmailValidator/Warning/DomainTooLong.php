<?php

namespace WappoVendor\Egulias\EmailValidator\Warning;

class DomainTooLong extends \WappoVendor\Egulias\EmailValidator\Warning\Warning
{
    const CODE = 255;
    public function __construct()
    {
        $this->message = 'Domain is too long, exceeds 255 chars';
        $this->rfcNumber = 5322;
    }
}
