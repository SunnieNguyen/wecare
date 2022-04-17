<?php

namespace WappoVendor\Egulias\EmailValidator\Exception;

class UnopenedComment extends \WappoVendor\Egulias\EmailValidator\Exception\InvalidEmail
{
    const CODE = 152;
    const REASON = "No opening comment token found";
}
