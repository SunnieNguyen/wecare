<?php

namespace WappoVendor\Egulias\EmailValidator\Exception;

class CRNoLF extends \WappoVendor\Egulias\EmailValidator\Exception\InvalidEmail
{
    const CODE = 150;
    const REASON = "Missing LF after CR";
}
