<?php

namespace WappoVendor\Egulias\EmailValidator\Warning;

class ObsoleteDTEXT extends \WappoVendor\Egulias\EmailValidator\Warning\Warning
{
    const CODE = 71;
    public function __construct()
    {
        $this->rfcNumber = 5322;
        $this->message = 'Obsolete DTEXT in domain literal';
    }
}
