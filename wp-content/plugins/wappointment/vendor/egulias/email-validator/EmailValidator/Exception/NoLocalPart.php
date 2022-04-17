<?php

namespace WappoVendor\Egulias\EmailValidator\Exception;

class NoLocalPart extends \WappoVendor\Egulias\EmailValidator\Exception\InvalidEmail
{
    const CODE = 130;
    const REASON = "No local part";
}
