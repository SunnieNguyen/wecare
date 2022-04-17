<?php

namespace WappoVendor\Egulias\EmailValidator;

use WappoVendor\Egulias\EmailValidator\Exception\ExpectingATEXT;
use WappoVendor\Egulias\EmailValidator\Exception\NoLocalPart;
use WappoVendor\Egulias\EmailValidator\Parser\DomainPart;
use WappoVendor\Egulias\EmailValidator\Parser\LocalPart;
use WappoVendor\Egulias\EmailValidator\Warning\EmailTooLong;
/**
 * EmailParser
 *
 * @author Eduardo Gulias Davis <me@egulias.com>
 */
class EmailParser
{
    const EMAIL_MAX_LENGTH = 254;
    protected $warnings;
    protected $domainPart = '';
    protected $localPart = '';
    protected $lexer;
    protected $localPartParser;
    protected $domainPartParser;
    public function __construct(\WappoVendor\Egulias\EmailValidator\EmailLexer $lexer)
    {
        $this->lexer = $lexer;
        $this->localPartParser = new \WappoVendor\Egulias\EmailValidator\Parser\LocalPart($this->lexer);
        $this->domainPartParser = new \WappoVendor\Egulias\EmailValidator\Parser\DomainPart($this->lexer);
        $this->warnings = new \SplObjectStorage();
    }
    /**
     * @param string $str
     * @return array
     */
    public function parse($str)
    {
        $this->lexer->setInput($str);
        if (!$this->hasAtToken()) {
            throw new \WappoVendor\Egulias\EmailValidator\Exception\NoLocalPart();
        }
        $this->localPartParser->parse($str);
        $this->domainPartParser->parse($str);
        $this->setParts($str);
        if ($this->lexer->hasInvalidTokens()) {
            throw new \WappoVendor\Egulias\EmailValidator\Exception\ExpectingATEXT();
        }
        return array('local' => $this->localPart, 'domain' => $this->domainPart);
    }
    public function getWarnings()
    {
        $localPartWarnings = $this->localPartParser->getWarnings();
        $domainPartWarnings = $this->domainPartParser->getWarnings();
        $this->warnings = \array_merge($localPartWarnings, $domainPartWarnings);
        $this->addLongEmailWarning($this->localPart, $this->domainPart);
        return $this->warnings;
    }
    public function getParsedDomainPart()
    {
        return $this->domainPart;
    }
    protected function setParts($email)
    {
        $parts = \explode('@', $email);
        $this->domainPart = $this->domainPartParser->getDomainPart();
        $this->localPart = $parts[0];
    }
    protected function hasAtToken()
    {
        $this->lexer->moveNext();
        $this->lexer->moveNext();
        if ($this->lexer->token['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::S_AT) {
            return false;
        }
        return true;
    }
    /**
     * @param string $localPart
     * @param string $parsedDomainPart
     */
    protected function addLongEmailWarning($localPart, $parsedDomainPart)
    {
        if (\strlen($localPart . '@' . $parsedDomainPart) > self::EMAIL_MAX_LENGTH) {
            $this->warnings[\WappoVendor\Egulias\EmailValidator\Warning\EmailTooLong::CODE] = new \WappoVendor\Egulias\EmailValidator\Warning\EmailTooLong();
        }
    }
}
