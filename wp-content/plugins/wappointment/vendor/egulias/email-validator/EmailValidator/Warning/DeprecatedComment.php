<?php

namespace WappoVendor\Egulias\EmailValidator\Warning;

class DeprecatedComment extends \WappoVendor\Egulias\EmailValidator\Warning\Warning
{
    const CODE = 37;
    public function __construct()
    {
        $this->message = 'Deprecated comments';
    }
}
