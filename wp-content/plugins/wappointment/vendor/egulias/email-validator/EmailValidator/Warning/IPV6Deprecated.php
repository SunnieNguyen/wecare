<?php

namespace WappoVendor\Egulias\EmailValidator\Warning;

class IPV6Deprecated extends \WappoVendor\Egulias\EmailValidator\Warning\Warning
{
    const CODE = 13;
    public function __construct()
    {
        $this->message = 'Deprecated form of IPV6';
        $this->rfcNumber = 5321;
    }
}
