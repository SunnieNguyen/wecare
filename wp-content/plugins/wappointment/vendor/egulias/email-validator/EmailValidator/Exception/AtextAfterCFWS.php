<?php

namespace WappoVendor\Egulias\EmailValidator\Exception;

class AtextAfterCFWS extends \WappoVendor\Egulias\EmailValidator\Exception\InvalidEmail
{
    const CODE = 133;
    const REASON = "ATEXT found after CFWS";
}
