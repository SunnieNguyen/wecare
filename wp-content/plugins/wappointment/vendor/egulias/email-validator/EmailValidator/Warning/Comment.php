<?php

namespace WappoVendor\Egulias\EmailValidator\Warning;

class Comment extends \WappoVendor\Egulias\EmailValidator\Warning\Warning
{
    const CODE = 17;
    public function __construct()
    {
        $this->message = "Comments found in this email";
    }
}
