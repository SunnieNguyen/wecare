<?php

namespace WappoVendor\Egulias\EmailValidator\Exception;

class DotAtStart extends \WappoVendor\Egulias\EmailValidator\Exception\InvalidEmail
{
    const CODE = 141;
    const REASON = "Found DOT at start";
}
