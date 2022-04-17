<?php

namespace WappoVendor\Egulias\EmailValidator\Parser;

use WappoVendor\Egulias\EmailValidator\EmailLexer;
use WappoVendor\Egulias\EmailValidator\Exception\AtextAfterCFWS;
use WappoVendor\Egulias\EmailValidator\Exception\ConsecutiveDot;
use WappoVendor\Egulias\EmailValidator\Exception\CRLFAtTheEnd;
use WappoVendor\Egulias\EmailValidator\Exception\CRLFX2;
use WappoVendor\Egulias\EmailValidator\Exception\CRNoLF;
use WappoVendor\Egulias\EmailValidator\Exception\ExpectingQPair;
use WappoVendor\Egulias\EmailValidator\Exception\ExpectingATEXT;
use WappoVendor\Egulias\EmailValidator\Exception\ExpectingCTEXT;
use WappoVendor\Egulias\EmailValidator\Exception\UnclosedComment;
use WappoVendor\Egulias\EmailValidator\Exception\UnclosedQuotedString;
use WappoVendor\Egulias\EmailValidator\Warning\CFWSNearAt;
use WappoVendor\Egulias\EmailValidator\Warning\CFWSWithFWS;
use WappoVendor\Egulias\EmailValidator\Warning\Comment;
use WappoVendor\Egulias\EmailValidator\Warning\QuotedPart;
use WappoVendor\Egulias\EmailValidator\Warning\QuotedString;
abstract class Parser
{
    protected $warnings = [];
    protected $lexer;
    protected $openedParenthesis = 0;
    public function __construct(\WappoVendor\Egulias\EmailValidator\EmailLexer $lexer)
    {
        $this->lexer = $lexer;
    }
    public function getWarnings()
    {
        return $this->warnings;
    }
    public abstract function parse($str);
    /** @return int */
    public function getOpenedParenthesis()
    {
        return $this->openedParenthesis;
    }
    /**
     * validateQuotedPair
     */
    protected function validateQuotedPair()
    {
        if (!($this->lexer->token['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::INVALID || $this->lexer->token['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::C_DEL)) {
            throw new \WappoVendor\Egulias\EmailValidator\Exception\ExpectingQPair();
        }
        $this->warnings[\WappoVendor\Egulias\EmailValidator\Warning\QuotedPart::CODE] = new \WappoVendor\Egulias\EmailValidator\Warning\QuotedPart($this->lexer->getPrevious()['type'], $this->lexer->token['type']);
    }
    protected function parseComments()
    {
        $this->openedParenthesis = 1;
        $this->isUnclosedComment();
        $this->warnings[\WappoVendor\Egulias\EmailValidator\Warning\Comment::CODE] = new \WappoVendor\Egulias\EmailValidator\Warning\Comment();
        while (!$this->lexer->isNextToken(\WappoVendor\Egulias\EmailValidator\EmailLexer::S_CLOSEPARENTHESIS)) {
            if ($this->lexer->isNextToken(\WappoVendor\Egulias\EmailValidator\EmailLexer::S_OPENPARENTHESIS)) {
                $this->openedParenthesis++;
            }
            $this->warnEscaping();
            $this->lexer->moveNext();
        }
        $this->lexer->moveNext();
        if ($this->lexer->isNextTokenAny(array(\WappoVendor\Egulias\EmailValidator\EmailLexer::GENERIC, \WappoVendor\Egulias\EmailValidator\EmailLexer::S_EMPTY))) {
            throw new \WappoVendor\Egulias\EmailValidator\Exception\ExpectingATEXT();
        }
        if ($this->lexer->isNextToken(\WappoVendor\Egulias\EmailValidator\EmailLexer::S_AT)) {
            $this->warnings[\WappoVendor\Egulias\EmailValidator\Warning\CFWSNearAt::CODE] = new \WappoVendor\Egulias\EmailValidator\Warning\CFWSNearAt();
        }
    }
    protected function isUnclosedComment()
    {
        try {
            $this->lexer->find(\WappoVendor\Egulias\EmailValidator\EmailLexer::S_CLOSEPARENTHESIS);
            return true;
        } catch (\RuntimeException $e) {
            throw new \WappoVendor\Egulias\EmailValidator\Exception\UnclosedComment();
        }
    }
    protected function parseFWS()
    {
        $previous = $this->lexer->getPrevious();
        $this->checkCRLFInFWS();
        if ($this->lexer->token['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::S_CR) {
            throw new \WappoVendor\Egulias\EmailValidator\Exception\CRNoLF();
        }
        if ($this->lexer->isNextToken(\WappoVendor\Egulias\EmailValidator\EmailLexer::GENERIC) && $previous['type'] !== \WappoVendor\Egulias\EmailValidator\EmailLexer::S_AT) {
            throw new \WappoVendor\Egulias\EmailValidator\Exception\AtextAfterCFWS();
        }
        if ($this->lexer->token['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::S_LF || $this->lexer->token['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::C_NUL) {
            throw new \WappoVendor\Egulias\EmailValidator\Exception\ExpectingCTEXT();
        }
        if ($this->lexer->isNextToken(\WappoVendor\Egulias\EmailValidator\EmailLexer::S_AT) || $previous['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::S_AT) {
            $this->warnings[\WappoVendor\Egulias\EmailValidator\Warning\CFWSNearAt::CODE] = new \WappoVendor\Egulias\EmailValidator\Warning\CFWSNearAt();
        } else {
            $this->warnings[\WappoVendor\Egulias\EmailValidator\Warning\CFWSWithFWS::CODE] = new \WappoVendor\Egulias\EmailValidator\Warning\CFWSWithFWS();
        }
    }
    protected function checkConsecutiveDots()
    {
        if ($this->lexer->token['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::S_DOT && $this->lexer->isNextToken(\WappoVendor\Egulias\EmailValidator\EmailLexer::S_DOT)) {
            throw new \WappoVendor\Egulias\EmailValidator\Exception\ConsecutiveDot();
        }
    }
    protected function isFWS()
    {
        if ($this->escaped()) {
            return false;
        }
        if ($this->lexer->token['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::S_SP || $this->lexer->token['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::S_HTAB || $this->lexer->token['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::S_CR || $this->lexer->token['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::S_LF || $this->lexer->token['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::CRLF) {
            return true;
        }
        return false;
    }
    protected function escaped()
    {
        $previous = $this->lexer->getPrevious();
        if ($previous['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::S_BACKSLASH && $this->lexer->token['type'] !== \WappoVendor\Egulias\EmailValidator\EmailLexer::GENERIC) {
            return true;
        }
        return false;
    }
    protected function warnEscaping()
    {
        if ($this->lexer->token['type'] !== \WappoVendor\Egulias\EmailValidator\EmailLexer::S_BACKSLASH) {
            return false;
        }
        if ($this->lexer->isNextToken(\WappoVendor\Egulias\EmailValidator\EmailLexer::GENERIC)) {
            throw new \WappoVendor\Egulias\EmailValidator\Exception\ExpectingATEXT();
        }
        if (!$this->lexer->isNextTokenAny(array(\WappoVendor\Egulias\EmailValidator\EmailLexer::S_SP, \WappoVendor\Egulias\EmailValidator\EmailLexer::S_HTAB, \WappoVendor\Egulias\EmailValidator\EmailLexer::C_DEL))) {
            return false;
        }
        $this->warnings[\WappoVendor\Egulias\EmailValidator\Warning\QuotedPart::CODE] = new \WappoVendor\Egulias\EmailValidator\Warning\QuotedPart($this->lexer->getPrevious()['type'], $this->lexer->token['type']);
        return true;
    }
    protected function checkDQUOTE($hasClosingQuote)
    {
        if ($this->lexer->token['type'] !== \WappoVendor\Egulias\EmailValidator\EmailLexer::S_DQUOTE) {
            return $hasClosingQuote;
        }
        if ($hasClosingQuote) {
            return $hasClosingQuote;
        }
        $previous = $this->lexer->getPrevious();
        if ($this->lexer->isNextToken(\WappoVendor\Egulias\EmailValidator\EmailLexer::GENERIC) && $previous['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::GENERIC) {
            throw new \WappoVendor\Egulias\EmailValidator\Exception\ExpectingATEXT();
        }
        try {
            $this->lexer->find(\WappoVendor\Egulias\EmailValidator\EmailLexer::S_DQUOTE);
            $hasClosingQuote = true;
        } catch (\Exception $e) {
            throw new \WappoVendor\Egulias\EmailValidator\Exception\UnclosedQuotedString();
        }
        $this->warnings[\WappoVendor\Egulias\EmailValidator\Warning\QuotedString::CODE] = new \WappoVendor\Egulias\EmailValidator\Warning\QuotedString($previous['value'], $this->lexer->token['value']);
        return $hasClosingQuote;
    }
    protected function checkCRLFInFWS()
    {
        if ($this->lexer->token['type'] !== \WappoVendor\Egulias\EmailValidator\EmailLexer::CRLF) {
            return;
        }
        if (!$this->lexer->isNextTokenAny(array(\WappoVendor\Egulias\EmailValidator\EmailLexer::S_SP, \WappoVendor\Egulias\EmailValidator\EmailLexer::S_HTAB))) {
            throw new \WappoVendor\Egulias\EmailValidator\Exception\CRLFX2();
        }
        if (!$this->lexer->isNextTokenAny(array(\WappoVendor\Egulias\EmailValidator\EmailLexer::S_SP, \WappoVendor\Egulias\EmailValidator\EmailLexer::S_HTAB))) {
            throw new \WappoVendor\Egulias\EmailValidator\Exception\CRLFAtTheEnd();
        }
    }
}
