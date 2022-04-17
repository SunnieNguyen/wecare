<?php

namespace WappoVendor\Egulias\EmailValidator\Exception;

class CRLFX2 extends \WappoVendor\Egulias\EmailValidator\Exception\InvalidEmail
{
    const CODE = 148;
    const REASON = "Folding whitespace CR LF found twice";
}
