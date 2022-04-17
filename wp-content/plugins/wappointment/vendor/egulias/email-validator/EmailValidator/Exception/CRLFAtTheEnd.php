<?php

namespace WappoVendor\Egulias\EmailValidator\Exception;

class CRLFAtTheEnd extends \WappoVendor\Egulias\EmailValidator\Exception\InvalidEmail
{
    const CODE = 149;
    const REASON = "CRLF at the end";
}
