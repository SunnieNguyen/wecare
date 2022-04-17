<?php

namespace WappoVendor\Egulias\EmailValidator\Warning;

class IPV6DoubleColon extends \WappoVendor\Egulias\EmailValidator\Warning\Warning
{
    const CODE = 73;
    public function __construct()
    {
        $this->message = 'Double colon found after IPV6 tag';
        $this->rfcNumber = 5322;
    }
}
