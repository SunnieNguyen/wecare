<?php

namespace WappoVendor\Egulias\EmailValidator\Validation;

use WappoVendor\Egulias\EmailValidator\EmailLexer;
use WappoVendor\Egulias\EmailValidator\Exception\InvalidEmail;
use WappoVendor\Egulias\EmailValidator\Validation\Error\SpoofEmail;
use Spoofchecker;
class SpoofCheckValidation implements \WappoVendor\Egulias\EmailValidator\Validation\EmailValidation
{
    /**
     * @var InvalidEmail
     */
    private $error;
    public function __construct()
    {
        if (!\extension_loaded('intl')) {
            throw new \LogicException(\sprintf('The %s class requires the Intl extension.', __CLASS__));
        }
    }
    public function isValid($email, \WappoVendor\Egulias\EmailValidator\EmailLexer $emailLexer)
    {
        $checker = new \Spoofchecker();
        $checker->setChecks(\Spoofchecker::SINGLE_SCRIPT);
        if ($checker->isSuspicious($email)) {
            $this->error = new \WappoVendor\Egulias\EmailValidator\Validation\Error\SpoofEmail();
        }
        return $this->error === null;
    }
    public function getError()
    {
        return $this->error;
    }
    public function getWarnings()
    {
        return [];
    }
}
