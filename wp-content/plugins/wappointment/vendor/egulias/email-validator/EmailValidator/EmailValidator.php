<?php

namespace WappoVendor\Egulias\EmailValidator;

use WappoVendor\Egulias\EmailValidator\Exception\InvalidEmail;
use WappoVendor\Egulias\EmailValidator\Validation\EmailValidation;
class EmailValidator
{
    /**
     * @var EmailLexer
     */
    private $lexer;
    /**
     * @var array
     */
    protected $warnings;
    /**
     * @var InvalidEmail
     */
    protected $error;
    public function __construct()
    {
        $this->lexer = new \WappoVendor\Egulias\EmailValidator\EmailLexer();
    }
    /**
     * @param string          $email
     * @param EmailValidation $emailValidation
     * @return bool
     */
    public function isValid($email, \WappoVendor\Egulias\EmailValidator\Validation\EmailValidation $emailValidation)
    {
        $isValid = $emailValidation->isValid($email, $this->lexer);
        $this->warnings = $emailValidation->getWarnings();
        $this->error = $emailValidation->getError();
        return $isValid;
    }
    /**
     * @return boolean
     */
    public function hasWarnings()
    {
        return !empty($this->warnings);
    }
    /**
     * @return array
     */
    public function getWarnings()
    {
        return $this->warnings;
    }
    /**
     * @return InvalidEmail
     */
    public function getError()
    {
        return $this->error;
    }
}
