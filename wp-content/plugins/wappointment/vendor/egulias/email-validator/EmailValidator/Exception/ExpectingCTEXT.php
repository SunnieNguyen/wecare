<?php

namespace WappoVendor\Egulias\EmailValidator\Exception;

class ExpectingCTEXT extends \WappoVendor\Egulias\EmailValidator\Exception\InvalidEmail
{
    const CODE = 139;
    const REASON = "Expecting CTEXT";
}
