<?php

namespace WappoVendor\Egulias\EmailValidator\Exception;

class DomainHyphened extends \WappoVendor\Egulias\EmailValidator\Exception\InvalidEmail
{
    const CODE = 144;
    const REASON = "Hyphen found in domain";
}
