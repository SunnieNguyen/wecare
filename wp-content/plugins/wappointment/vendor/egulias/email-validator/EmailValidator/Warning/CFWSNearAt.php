<?php

namespace WappoVendor\Egulias\EmailValidator\Warning;

class CFWSNearAt extends \WappoVendor\Egulias\EmailValidator\Warning\Warning
{
    const CODE = 49;
    public function __construct()
    {
        $this->message = "Deprecated folding white space near @";
    }
}
