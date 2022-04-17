<?php

namespace WappoVendor\Egulias\EmailValidator\Validation;

use WappoVendor\Egulias\EmailValidator\EmailLexer;
use WappoVendor\Egulias\EmailValidator\Exception\InvalidEmail;
use WappoVendor\Egulias\EmailValidator\Validation\Error\RFCWarnings;
class NoRFCWarningsValidation extends \WappoVendor\Egulias\EmailValidator\Validation\RFCValidation
{
    /**
     * @var InvalidEmail
     */
    private $error;
    /**
     * {@inheritdoc}
     */
    public function isValid($email, \WappoVendor\Egulias\EmailValidator\EmailLexer $emailLexer)
    {
        if (!parent::isValid($email, $emailLexer)) {
            return false;
        }
        if (empty($this->getWarnings())) {
            return true;
        }
        $this->error = new \WappoVendor\Egulias\EmailValidator\Validation\Error\RFCWarnings();
        return false;
    }
    /**
     * {@inheritdoc}
     */
    public function getError()
    {
        return $this->error ?: parent::getError();
    }
}
