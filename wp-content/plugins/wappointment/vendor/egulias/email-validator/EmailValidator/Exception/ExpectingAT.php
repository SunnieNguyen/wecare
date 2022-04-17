<?php

namespace WappoVendor\Egulias\EmailValidator\Exception;

class ExpectingAT extends \WappoVendor\Egulias\EmailValidator\Exception\InvalidEmail
{
    const CODE = 202;
    const REASON = "Expecting AT '@' ";
}
