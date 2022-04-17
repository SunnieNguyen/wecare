<?php

namespace WappoVendor\Egulias\EmailValidator\Warning;

class IPV6ColonStart extends \WappoVendor\Egulias\EmailValidator\Warning\Warning
{
    const CODE = 76;
    public function __construct()
    {
        $this->message = ':: found at the start of the domain literal';
        $this->rfcNumber = 5322;
    }
}
