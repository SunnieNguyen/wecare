<?php

namespace WappoVendor\Egulias\EmailValidator\Exception;

class DotAtEnd extends \WappoVendor\Egulias\EmailValidator\Exception\InvalidEmail
{
    const CODE = 142;
    const REASON = "Dot at the end";
}
