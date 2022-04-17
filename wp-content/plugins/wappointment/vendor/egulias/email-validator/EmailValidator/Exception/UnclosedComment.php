<?php

namespace WappoVendor\Egulias\EmailValidator\Exception;

class UnclosedComment extends \WappoVendor\Egulias\EmailValidator\Exception\InvalidEmail
{
    const CODE = 146;
    const REASON = "No colosing comment token found";
}
