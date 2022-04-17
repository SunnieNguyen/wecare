<?php

namespace WappoVendor\Egulias\EmailValidator\Exception;

class CharNotAllowed extends \WappoVendor\Egulias\EmailValidator\Exception\InvalidEmail
{
    const CODE = 201;
    const REASON = "Non allowed character in domain";
}
