<?php

namespace WappoVendor\Egulias\EmailValidator\Warning;

class TLD extends \WappoVendor\Egulias\EmailValidator\Warning\Warning
{
    const CODE = 9;
    public function __construct()
    {
        $this->message = "RFC5321, TLD";
    }
}
