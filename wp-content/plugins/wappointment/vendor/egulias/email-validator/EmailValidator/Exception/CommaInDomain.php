<?php

namespace WappoVendor\Egulias\EmailValidator\Exception;

class CommaInDomain extends \WappoVendor\Egulias\EmailValidator\Exception\InvalidEmail
{
    const CODE = 200;
    const REASON = "Comma ',' is not allowed in domain part";
}
