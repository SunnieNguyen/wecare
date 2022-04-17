<?php

namespace WappoVendor\Egulias\EmailValidator\Validation;

use WappoVendor\Egulias\EmailValidator\EmailLexer;
use WappoVendor\Egulias\EmailValidator\Exception\InvalidEmail;
use WappoVendor\Egulias\EmailValidator\Warning\NoDNSMXRecord;
use WappoVendor\Egulias\EmailValidator\Exception\NoDNSRecord;
class DNSCheckValidation implements \WappoVendor\Egulias\EmailValidator\Validation\EmailValidation
{
    /**
     * @var array
     */
    private $warnings = [];
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
        // use the input to check DNS if we cannot extract something similar to a domain
        $host = $email;
        // Arguable pattern to extract the domain. Not aiming to validate the domain nor the email
        if (false !== ($lastAtPos = \strrpos($email, '@'))) {
            $host = \substr($email, $lastAtPos + 1);
        }
        return $this->checkDNS($host);
    }
    public function getError()
    {
        return $this->error;
    }
    public function getWarnings()
    {
        return $this->warnings;
    }
    protected function checkDNS($host)
    {
        $variant = \INTL_IDNA_VARIANT_2003;
        if (\defined('INTL_IDNA_VARIANT_UTS46')) {
            $variant = \INTL_IDNA_VARIANT_UTS46;
        }
        $host = \rtrim(\idn_to_ascii($host, \IDNA_DEFAULT, $variant), '.') . '.';
        $Aresult = true;
        $MXresult = \checkdnsrr($host, 'MX');
        if (!$MXresult) {
            $this->warnings[\WappoVendor\Egulias\EmailValidator\Warning\NoDNSMXRecord::CODE] = new \WappoVendor\Egulias\EmailValidator\Warning\NoDNSMXRecord();
            $Aresult = \checkdnsrr($host, 'A') || \checkdnsrr($host, 'AAAA');
            if (!$Aresult) {
                $this->error = new \WappoVendor\Egulias\EmailValidator\Exception\NoDNSRecord();
            }
        }
        return $MXresult || $Aresult;
    }
}
