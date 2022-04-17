<?php

namespace WappoVendor\Egulias\EmailValidator\Warning;

class QuotedString extends \WappoVendor\Egulias\EmailValidator\Warning\Warning
{
    const CODE = 11;
    public function __construct($prevToken, $postToken)
    {
        $this->message = "Quoted String found between {$prevToken} and {$postToken}";
    }
}
