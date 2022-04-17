<?php

namespace WappoVendor\Egulias\EmailValidator\Validation\Error;

use WappoVendor\Egulias\EmailValidator\Exception\InvalidEmail;
class SpoofEmail extends \WappoVendor\Egulias\EmailValidator\Exception\InvalidEmail
{
    const CODE = 998;
    const REASON = "The email contains mixed UTF8 chars that makes it suspicious";
}
