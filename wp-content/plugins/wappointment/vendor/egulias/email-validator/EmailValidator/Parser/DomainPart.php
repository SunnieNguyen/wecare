<?php

namespace WappoVendor\Egulias\EmailValidator\Parser;

use WappoVendor\Egulias\EmailValidator\EmailLexer;
use WappoVendor\Egulias\EmailValidator\Exception\CharNotAllowed;
use WappoVendor\Egulias\EmailValidator\Exception\CommaInDomain;
use WappoVendor\Egulias\EmailValidator\Exception\ConsecutiveAt;
use WappoVendor\Egulias\EmailValidator\Exception\CRLFAtTheEnd;
use WappoVendor\Egulias\EmailValidator\Exception\CRNoLF;
use WappoVendor\Egulias\EmailValidator\Exception\DomainHyphened;
use WappoVendor\Egulias\EmailValidator\Exception\DotAtEnd;
use WappoVendor\Egulias\EmailValidator\Exception\DotAtStart;
use WappoVendor\Egulias\EmailValidator\Exception\ExpectingATEXT;
use WappoVendor\Egulias\EmailValidator\Exception\ExpectingDomainLiteralClose;
use WappoVendor\Egulias\EmailValidator\Exception\ExpectingDTEXT;
use WappoVendor\Egulias\EmailValidator\Exception\NoDomainPart;
use WappoVendor\Egulias\EmailValidator\Exception\UnopenedComment;
use WappoVendor\Egulias\EmailValidator\Warning\AddressLiteral;
use WappoVendor\Egulias\EmailValidator\Warning\CFWSWithFWS;
use WappoVendor\Egulias\EmailValidator\Warning\DeprecatedComment;
use WappoVendor\Egulias\EmailValidator\Warning\DomainLiteral;
use WappoVendor\Egulias\EmailValidator\Warning\DomainTooLong;
use WappoVendor\Egulias\EmailValidator\Warning\IPV6BadChar;
use WappoVendor\Egulias\EmailValidator\Warning\IPV6ColonEnd;
use WappoVendor\Egulias\EmailValidator\Warning\IPV6ColonStart;
use WappoVendor\Egulias\EmailValidator\Warning\IPV6Deprecated;
use WappoVendor\Egulias\EmailValidator\Warning\IPV6DoubleColon;
use WappoVendor\Egulias\EmailValidator\Warning\IPV6GroupCount;
use WappoVendor\Egulias\EmailValidator\Warning\IPV6MaxGroups;
use WappoVendor\Egulias\EmailValidator\Warning\LabelTooLong;
use WappoVendor\Egulias\EmailValidator\Warning\ObsoleteDTEXT;
use WappoVendor\Egulias\EmailValidator\Warning\TLD;
class DomainPart extends \WappoVendor\Egulias\EmailValidator\Parser\Parser
{
    const DOMAIN_MAX_LENGTH = 254;
    protected $domainPart = '';
    public function parse($domainPart)
    {
        $this->lexer->moveNext();
        $this->performDomainStartChecks();
        $domain = $this->doParseDomainPart();
        $prev = $this->lexer->getPrevious();
        $length = \strlen($domain);
        if ($prev['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::S_DOT) {
            throw new \WappoVendor\Egulias\EmailValidator\Exception\DotAtEnd();
        }
        if ($prev['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::S_HYPHEN) {
            throw new \WappoVendor\Egulias\EmailValidator\Exception\DomainHyphened();
        }
        if ($length > self::DOMAIN_MAX_LENGTH) {
            $this->warnings[\WappoVendor\Egulias\EmailValidator\Warning\DomainTooLong::CODE] = new \WappoVendor\Egulias\EmailValidator\Warning\DomainTooLong();
        }
        if ($prev['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::S_CR) {
            throw new \WappoVendor\Egulias\EmailValidator\Exception\CRLFAtTheEnd();
        }
        $this->domainPart = $domain;
    }
    private function performDomainStartChecks()
    {
        $this->checkInvalidTokensAfterAT();
        $this->checkEmptyDomain();
        if ($this->lexer->token['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::S_OPENPARENTHESIS) {
            $this->warnings[\WappoVendor\Egulias\EmailValidator\Warning\DeprecatedComment::CODE] = new \WappoVendor\Egulias\EmailValidator\Warning\DeprecatedComment();
            $this->parseDomainComments();
        }
    }
    private function checkEmptyDomain()
    {
        $thereIsNoDomain = $this->lexer->token['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::S_EMPTY || $this->lexer->token['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::S_SP && !$this->lexer->isNextToken(\WappoVendor\Egulias\EmailValidator\EmailLexer::GENERIC);
        if ($thereIsNoDomain) {
            throw new \WappoVendor\Egulias\EmailValidator\Exception\NoDomainPart();
        }
    }
    private function checkInvalidTokensAfterAT()
    {
        if ($this->lexer->token['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::S_DOT) {
            throw new \WappoVendor\Egulias\EmailValidator\Exception\DotAtStart();
        }
        if ($this->lexer->token['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::S_HYPHEN) {
            throw new \WappoVendor\Egulias\EmailValidator\Exception\DomainHyphened();
        }
    }
    public function getDomainPart()
    {
        return $this->domainPart;
    }
    public function checkIPV6Tag($addressLiteral, $maxGroups = 8)
    {
        $prev = $this->lexer->getPrevious();
        if ($prev['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::S_COLON) {
            $this->warnings[\WappoVendor\Egulias\EmailValidator\Warning\IPV6ColonEnd::CODE] = new \WappoVendor\Egulias\EmailValidator\Warning\IPV6ColonEnd();
        }
        $IPv6 = \substr($addressLiteral, 5);
        //Daniel Marschall's new IPv6 testing strategy
        $matchesIP = \explode(':', $IPv6);
        $groupCount = \count($matchesIP);
        $colons = \strpos($IPv6, '::');
        if (\count(\preg_grep('/^[0-9A-Fa-f]{0,4}$/', $matchesIP, \PREG_GREP_INVERT)) !== 0) {
            $this->warnings[\WappoVendor\Egulias\EmailValidator\Warning\IPV6BadChar::CODE] = new \WappoVendor\Egulias\EmailValidator\Warning\IPV6BadChar();
        }
        if ($colons === false) {
            // We need exactly the right number of groups
            if ($groupCount !== $maxGroups) {
                $this->warnings[\WappoVendor\Egulias\EmailValidator\Warning\IPV6GroupCount::CODE] = new \WappoVendor\Egulias\EmailValidator\Warning\IPV6GroupCount();
            }
            return;
        }
        if ($colons !== \strrpos($IPv6, '::')) {
            $this->warnings[\WappoVendor\Egulias\EmailValidator\Warning\IPV6DoubleColon::CODE] = new \WappoVendor\Egulias\EmailValidator\Warning\IPV6DoubleColon();
            return;
        }
        if ($colons === 0 || $colons === \strlen($IPv6) - 2) {
            // RFC 4291 allows :: at the start or end of an address
            //with 7 other groups in addition
            ++$maxGroups;
        }
        if ($groupCount > $maxGroups) {
            $this->warnings[\WappoVendor\Egulias\EmailValidator\Warning\IPV6MaxGroups::CODE] = new \WappoVendor\Egulias\EmailValidator\Warning\IPV6MaxGroups();
        } elseif ($groupCount === $maxGroups) {
            $this->warnings[\WappoVendor\Egulias\EmailValidator\Warning\IPV6Deprecated::CODE] = new \WappoVendor\Egulias\EmailValidator\Warning\IPV6Deprecated();
        }
    }
    protected function doParseDomainPart()
    {
        $domain = '';
        $openedParenthesis = 0;
        do {
            $prev = $this->lexer->getPrevious();
            $this->checkNotAllowedChars($this->lexer->token);
            if ($this->lexer->token['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::S_OPENPARENTHESIS) {
                $this->parseComments();
                $openedParenthesis += $this->getOpenedParenthesis();
                $this->lexer->moveNext();
                $tmpPrev = $this->lexer->getPrevious();
                if ($tmpPrev['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::S_CLOSEPARENTHESIS) {
                    $openedParenthesis--;
                }
            }
            if ($this->lexer->token['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::S_CLOSEPARENTHESIS) {
                if ($openedParenthesis === 0) {
                    throw new \WappoVendor\Egulias\EmailValidator\Exception\UnopenedComment();
                } else {
                    $openedParenthesis--;
                }
            }
            $this->checkConsecutiveDots();
            $this->checkDomainPartExceptions($prev);
            if ($this->hasBrackets()) {
                $this->parseDomainLiteral();
            }
            $this->checkLabelLength($prev);
            if ($this->isFWS()) {
                $this->parseFWS();
            }
            $domain .= $this->lexer->token['value'];
            $this->lexer->moveNext();
        } while (null !== $this->lexer->token['type']);
        return $domain;
    }
    private function checkNotAllowedChars($token)
    {
        $notAllowed = [\WappoVendor\Egulias\EmailValidator\EmailLexer::S_BACKSLASH => true, \WappoVendor\Egulias\EmailValidator\EmailLexer::S_SLASH => true];
        if (isset($notAllowed[$token['type']])) {
            throw new \WappoVendor\Egulias\EmailValidator\Exception\CharNotAllowed();
        }
    }
    protected function parseDomainLiteral()
    {
        if ($this->lexer->isNextToken(\WappoVendor\Egulias\EmailValidator\EmailLexer::S_COLON)) {
            $this->warnings[\WappoVendor\Egulias\EmailValidator\Warning\IPV6ColonStart::CODE] = new \WappoVendor\Egulias\EmailValidator\Warning\IPV6ColonStart();
        }
        if ($this->lexer->isNextToken(\WappoVendor\Egulias\EmailValidator\EmailLexer::S_IPV6TAG)) {
            $lexer = clone $this->lexer;
            $lexer->moveNext();
            if ($lexer->isNextToken(\WappoVendor\Egulias\EmailValidator\EmailLexer::S_DOUBLECOLON)) {
                $this->warnings[\WappoVendor\Egulias\EmailValidator\Warning\IPV6ColonStart::CODE] = new \WappoVendor\Egulias\EmailValidator\Warning\IPV6ColonStart();
            }
        }
        return $this->doParseDomainLiteral();
    }
    protected function doParseDomainLiteral()
    {
        $IPv6TAG = false;
        $addressLiteral = '';
        do {
            if ($this->lexer->token['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::C_NUL) {
                throw new \WappoVendor\Egulias\EmailValidator\Exception\ExpectingDTEXT();
            }
            if ($this->lexer->token['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::INVALID || $this->lexer->token['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::C_DEL || $this->lexer->token['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::S_LF) {
                $this->warnings[\WappoVendor\Egulias\EmailValidator\Warning\ObsoleteDTEXT::CODE] = new \WappoVendor\Egulias\EmailValidator\Warning\ObsoleteDTEXT();
            }
            if ($this->lexer->isNextTokenAny(array(\WappoVendor\Egulias\EmailValidator\EmailLexer::S_OPENQBRACKET, \WappoVendor\Egulias\EmailValidator\EmailLexer::S_OPENBRACKET))) {
                throw new \WappoVendor\Egulias\EmailValidator\Exception\ExpectingDTEXT();
            }
            if ($this->lexer->isNextTokenAny(array(\WappoVendor\Egulias\EmailValidator\EmailLexer::S_HTAB, \WappoVendor\Egulias\EmailValidator\EmailLexer::S_SP, $this->lexer->token['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::CRLF))) {
                $this->warnings[\WappoVendor\Egulias\EmailValidator\Warning\CFWSWithFWS::CODE] = new \WappoVendor\Egulias\EmailValidator\Warning\CFWSWithFWS();
                $this->parseFWS();
            }
            if ($this->lexer->isNextToken(\WappoVendor\Egulias\EmailValidator\EmailLexer::S_CR)) {
                throw new \WappoVendor\Egulias\EmailValidator\Exception\CRNoLF();
            }
            if ($this->lexer->token['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::S_BACKSLASH) {
                $this->warnings[\WappoVendor\Egulias\EmailValidator\Warning\ObsoleteDTEXT::CODE] = new \WappoVendor\Egulias\EmailValidator\Warning\ObsoleteDTEXT();
                $addressLiteral .= $this->lexer->token['value'];
                $this->lexer->moveNext();
                $this->validateQuotedPair();
            }
            if ($this->lexer->token['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::S_IPV6TAG) {
                $IPv6TAG = true;
            }
            if ($this->lexer->token['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::S_CLOSEQBRACKET) {
                break;
            }
            $addressLiteral .= $this->lexer->token['value'];
        } while ($this->lexer->moveNext());
        $addressLiteral = \str_replace('[', '', $addressLiteral);
        $addressLiteral = $this->checkIPV4Tag($addressLiteral);
        if (false === $addressLiteral) {
            return $addressLiteral;
        }
        if (!$IPv6TAG) {
            $this->warnings[\WappoVendor\Egulias\EmailValidator\Warning\DomainLiteral::CODE] = new \WappoVendor\Egulias\EmailValidator\Warning\DomainLiteral();
            return $addressLiteral;
        }
        $this->warnings[\WappoVendor\Egulias\EmailValidator\Warning\AddressLiteral::CODE] = new \WappoVendor\Egulias\EmailValidator\Warning\AddressLiteral();
        $this->checkIPV6Tag($addressLiteral);
        return $addressLiteral;
    }
    protected function checkIPV4Tag($addressLiteral)
    {
        $matchesIP = array();
        // Extract IPv4 part from the end of the address-literal (if there is one)
        if (\preg_match('/\\b(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/', $addressLiteral, $matchesIP) > 0) {
            $index = \strrpos($addressLiteral, $matchesIP[0]);
            if ($index === 0) {
                $this->warnings[\WappoVendor\Egulias\EmailValidator\Warning\AddressLiteral::CODE] = new \WappoVendor\Egulias\EmailValidator\Warning\AddressLiteral();
                return false;
            }
            // Convert IPv4 part to IPv6 format for further testing
            $addressLiteral = \substr($addressLiteral, 0, $index) . '0:0';
        }
        return $addressLiteral;
    }
    protected function checkDomainPartExceptions($prev)
    {
        $invalidDomainTokens = array(\WappoVendor\Egulias\EmailValidator\EmailLexer::S_DQUOTE => true, \WappoVendor\Egulias\EmailValidator\EmailLexer::S_SEMICOLON => true, \WappoVendor\Egulias\EmailValidator\EmailLexer::S_GREATERTHAN => true, \WappoVendor\Egulias\EmailValidator\EmailLexer::S_LOWERTHAN => true);
        if (isset($invalidDomainTokens[$this->lexer->token['type']])) {
            throw new \WappoVendor\Egulias\EmailValidator\Exception\ExpectingATEXT();
        }
        if ($this->lexer->token['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::S_COMMA) {
            throw new \WappoVendor\Egulias\EmailValidator\Exception\CommaInDomain();
        }
        if ($this->lexer->token['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::S_AT) {
            throw new \WappoVendor\Egulias\EmailValidator\Exception\ConsecutiveAt();
        }
        if ($this->lexer->token['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::S_OPENQBRACKET && $prev['type'] !== \WappoVendor\Egulias\EmailValidator\EmailLexer::S_AT) {
            throw new \WappoVendor\Egulias\EmailValidator\Exception\ExpectingATEXT();
        }
        if ($this->lexer->token['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::S_HYPHEN && $this->lexer->isNextToken(\WappoVendor\Egulias\EmailValidator\EmailLexer::S_DOT)) {
            throw new \WappoVendor\Egulias\EmailValidator\Exception\DomainHyphened();
        }
        if ($this->lexer->token['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::S_BACKSLASH && $this->lexer->isNextToken(\WappoVendor\Egulias\EmailValidator\EmailLexer::GENERIC)) {
            throw new \WappoVendor\Egulias\EmailValidator\Exception\ExpectingATEXT();
        }
    }
    protected function hasBrackets()
    {
        if ($this->lexer->token['type'] !== \WappoVendor\Egulias\EmailValidator\EmailLexer::S_OPENBRACKET) {
            return false;
        }
        try {
            $this->lexer->find(\WappoVendor\Egulias\EmailValidator\EmailLexer::S_CLOSEBRACKET);
        } catch (\RuntimeException $e) {
            throw new \WappoVendor\Egulias\EmailValidator\Exception\ExpectingDomainLiteralClose();
        }
        return true;
    }
    protected function checkLabelLength($prev)
    {
        if ($this->lexer->token['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::S_DOT && $prev['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::GENERIC && \strlen($prev['value']) > 63) {
            $this->warnings[\WappoVendor\Egulias\EmailValidator\Warning\LabelTooLong::CODE] = new \WappoVendor\Egulias\EmailValidator\Warning\LabelTooLong();
        }
    }
    protected function parseDomainComments()
    {
        $this->isUnclosedComment();
        while (!$this->lexer->isNextToken(\WappoVendor\Egulias\EmailValidator\EmailLexer::S_CLOSEPARENTHESIS)) {
            $this->warnEscaping();
            $this->lexer->moveNext();
        }
        $this->lexer->moveNext();
        if ($this->lexer->isNextToken(\WappoVendor\Egulias\EmailValidator\EmailLexer::S_DOT)) {
            throw new \WappoVendor\Egulias\EmailValidator\Exception\ExpectingATEXT();
        }
    }
    protected function addTLDWarnings()
    {
        if ($this->warnings[\WappoVendor\Egulias\EmailValidator\Warning\DomainLiteral::CODE]) {
            $this->warnings[\WappoVendor\Egulias\EmailValidator\Warning\TLD::CODE] = new \WappoVendor\Egulias\EmailValidator\Warning\TLD();
        }
    }
}
