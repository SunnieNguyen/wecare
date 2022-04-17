<?php

namespace WappoVendor\Egulias\EmailValidator\Warning;

class NoDNSMXRecord extends \WappoVendor\Egulias\EmailValidator\Warning\Warning
{
    const CODE = 6;
    public function __construct()
    {
        $this->message = 'No MX DSN record was found for this email';
        $this->rfcNumber = 5321;
    }
}
