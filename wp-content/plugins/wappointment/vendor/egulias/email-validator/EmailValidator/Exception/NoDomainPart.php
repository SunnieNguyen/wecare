<?php

namespace WappoVendor\Egulias\EmailValidator\Exception;

class NoDomainPart extends \WappoVendor\Egulias\EmailValidator\Exception\InvalidEmail
{
    const CODE = 131;
    const REASON = "No Domain part";
}
