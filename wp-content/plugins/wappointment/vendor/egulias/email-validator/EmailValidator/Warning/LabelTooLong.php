<?php

namespace WappoVendor\Egulias\EmailValidator\Warning;

class LabelTooLong extends \WappoVendor\Egulias\EmailValidator\Warning\Warning
{
    const CODE = 63;
    public function __construct()
    {
        $this->message = 'Label too long';
        $this->rfcNumber = 5322;
    }
}
