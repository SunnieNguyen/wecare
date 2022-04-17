<?php

namespace WappoVendor\Egulias\EmailValidator\Validation;

use WappoVendor\Egulias\EmailValidator\Exception\InvalidEmail;
class MultipleErrors extends \WappoVendor\Egulias\EmailValidator\Exception\InvalidEmail
{
    const CODE = 999;
    const REASON = "Accumulated errors for multiple validations";
    /**
     * @var array
     */
    private $errors = [];
    public function __construct(array $errors)
    {
        $this->errors = $errors;
        parent::__construct();
    }
    public function getErrors()
    {
        return $this->errors;
    }
}
