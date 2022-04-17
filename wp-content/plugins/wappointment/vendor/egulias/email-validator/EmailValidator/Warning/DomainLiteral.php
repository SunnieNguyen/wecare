<?php

namespace WappoVendor\Egulias\EmailValidator\Warning;

class DomainLiteral extends \WappoVendor\Egulias\EmailValidator\Warning\Warning
{
    const CODE = 70;
    public function __construct()
    {
        $this->message = 'Domain Literal';
        $this->rfcNumber = 5322;
    }
}
