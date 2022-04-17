<?php

namespace WappoVendor\Egulias\EmailValidator\Validation;

use WappoVendor\Egulias\EmailValidator\EmailLexer;
use WappoVendor\Egulias\EmailValidator\EmailParser;
use WappoVendor\Egulias\EmailValidator\Exception\InvalidEmail;
class RFCValidation implements \WappoVendor\Egulias\EmailValidator\Validation\EmailValidation
{
    /**
     * @var EmailParser
     */
    private $parser;
    /**
     * @var array
     */
    private $warnings = [];
    /**
     * @var InvalidEmail
     */
    private $error;
    public function isValid($email, \WappoVendor\Egulias\EmailValidator\EmailLexer $emailLexer)
    {
        $this->parser = new \WappoVendor\Egulias\EmailValidator\EmailParser($emailLexer);
        try {
            $this->parser->parse((string) $email);
        } catch (\WappoVendor\Egulias\EmailValidator\Exception\InvalidEmail $invalid) {
            $this->error = $invalid;
            return false;
        }
        $this->warnings = $this->parser->getWarnings();
        return true;
    }
    public function getError()
    {
        return $this->error;
    }
    public function getWarnings()
    {
        return $this->warnings;
    }
}
