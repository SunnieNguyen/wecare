<?php

namespace WappoVendor\Egulias\EmailValidator\Warning;

class AddressLiteral extends \WappoVendor\Egulias\EmailValidator\Warning\Warning
{
    const CODE = 12;
    public function __construct()
    {
        $this->message = 'Address literal in domain part';
        $this->rfcNumber = 5321;
    }
}
