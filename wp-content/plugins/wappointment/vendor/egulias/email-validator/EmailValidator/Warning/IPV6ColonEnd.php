<?php

namespace WappoVendor\Egulias\EmailValidator\Warning;

class IPV6ColonEnd extends \WappoVendor\Egulias\EmailValidator\Warning\Warning
{
    const CODE = 77;
    public function __construct()
    {
        $this->message = ':: found at the end of the domain literal';
        $this->rfcNumber = 5322;
    }
}
