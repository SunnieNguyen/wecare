<?php

namespace WappoVendor\Egulias\EmailValidator\Parser;

use WappoVendor\Egulias\EmailValidator\Exception\DotAtEnd;
use WappoVendor\Egulias\EmailValidator\Exception\DotAtStart;
use WappoVendor\Egulias\EmailValidator\EmailLexer;
use WappoVendor\Egulias\EmailValidator\EmailValidator;
use WappoVendor\Egulias\EmailValidator\Exception\ExpectingAT;
use WappoVendor\Egulias\EmailValidator\Exception\ExpectingATEXT;
use WappoVendor\Egulias\EmailValidator\Exception\UnclosedQuotedString;
use WappoVendor\Egulias\EmailValidator\Exception\UnopenedComment;
use WappoVendor\Egulias\EmailValidator\Warning\CFWSWithFWS;
use WappoVendor\Egulias\EmailValidator\Warning\LocalTooLong;
class LocalPart extends \WappoVendor\Egulias\EmailValidator\Parser\Parser
{
    public function parse($localPart)
    {
        $parseDQuote = true;
        $closingQuote = false;
        $openedParenthesis = 0;
        while ($this->lexer->token['type'] !== \WappoVendor\Egulias\EmailValidator\EmailLexer::S_AT && null !== $this->lexer->token['type']) {
            if ($this->lexer->token['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::S_DOT && null === $this->lexer->getPrevious()['type']) {
                throw new \WappoVendor\Egulias\EmailValidator\Exception\DotAtStart();
            }
            $closingQuote = $this->checkDQUOTE($closingQuote);
            if ($closingQuote && $parseDQuote) {
                $parseDQuote = $this->parseDoubleQuote();
            }
            if ($this->lexer->token['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::S_OPENPARENTHESIS) {
                $this->parseComments();
                $openedParenthesis += $this->getOpenedParenthesis();
            }
            if ($this->lexer->token['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::S_CLOSEPARENTHESIS) {
                if ($openedParenthesis === 0) {
                    throw new \WappoVendor\Egulias\EmailValidator\Exception\UnopenedComment();
                } else {
                    $openedParenthesis--;
                }
            }
            $this->checkConsecutiveDots();
            if ($this->lexer->token['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::S_DOT && $this->lexer->isNextToken(\WappoVendor\Egulias\EmailValidator\EmailLexer::S_AT)) {
                throw new \WappoVendor\Egulias\EmailValidator\Exception\DotAtEnd();
            }
            $this->warnEscaping();
            $this->isInvalidToken($this->lexer->token, $closingQuote);
            if ($this->isFWS()) {
                $this->parseFWS();
            }
            $this->lexer->moveNext();
        }
        $prev = $this->lexer->getPrevious();
        if (\strlen($prev['value']) > \WappoVendor\Egulias\EmailValidator\Warning\LocalTooLong::LOCAL_PART_LENGTH) {
            $this->warnings[\WappoVendor\Egulias\EmailValidator\Warning\LocalTooLong::CODE] = new \WappoVendor\Egulias\EmailValidator\Warning\LocalTooLong();
        }
    }
    protected function parseDoubleQuote()
    {
        $parseAgain = true;
        $special = array(\WappoVendor\Egulias\EmailValidator\EmailLexer::S_CR => true, \WappoVendor\Egulias\EmailValidator\EmailLexer::S_HTAB => true, \WappoVendor\Egulias\EmailValidator\EmailLexer::S_LF => true);
        $invalid = array(\WappoVendor\Egulias\EmailValidator\EmailLexer::C_NUL => true, \WappoVendor\Egulias\EmailValidator\EmailLexer::S_HTAB => true, \WappoVendor\Egulias\EmailValidator\EmailLexer::S_CR => true, \WappoVendor\Egulias\EmailValidator\EmailLexer::S_LF => true);
        $setSpecialsWarning = true;
        $this->lexer->moveNext();
        while ($this->lexer->token['type'] !== \WappoVendor\Egulias\EmailValidator\EmailLexer::S_DQUOTE && null !== $this->lexer->token['type']) {
            $parseAgain = false;
            if (isset($special[$this->lexer->token['type']]) && $setSpecialsWarning) {
                $this->warnings[\WappoVendor\Egulias\EmailValidator\Warning\CFWSWithFWS::CODE] = new \WappoVendor\Egulias\EmailValidator\Warning\CFWSWithFWS();
                $setSpecialsWarning = false;
            }
            if ($this->lexer->token['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::S_BACKSLASH && $this->lexer->isNextToken(\WappoVendor\Egulias\EmailValidator\EmailLexer::S_DQUOTE)) {
                $this->lexer->moveNext();
            }
            $this->lexer->moveNext();
            if (!$this->escaped() && isset($invalid[$this->lexer->token['type']])) {
                throw new \WappoVendor\Egulias\EmailValidator\Exception\ExpectingATEXT();
            }
        }
        $prev = $this->lexer->getPrevious();
        if ($prev['type'] === \WappoVendor\Egulias\EmailValidator\EmailLexer::S_BACKSLASH) {
            if (!$this->checkDQUOTE(false)) {
                throw new \WappoVendor\Egulias\EmailValidator\Exception\UnclosedQuotedString();
            }
        }
        if (!$this->lexer->isNextToken(\WappoVendor\Egulias\EmailValidator\EmailLexer::S_AT) && $prev['type'] !== \WappoVendor\Egulias\EmailValidator\EmailLexer::S_BACKSLASH) {
            throw new \WappoVendor\Egulias\EmailValidator\Exception\ExpectingAT();
        }
        return $parseAgain;
    }
    protected function isInvalidToken($token, $closingQuote)
    {
        $forbidden = array(\WappoVendor\Egulias\EmailValidator\EmailLexer::S_COMMA, \WappoVendor\Egulias\EmailValidator\EmailLexer::S_CLOSEBRACKET, \WappoVendor\Egulias\EmailValidator\EmailLexer::S_OPENBRACKET, \WappoVendor\Egulias\EmailValidator\EmailLexer::S_GREATERTHAN, \WappoVendor\Egulias\EmailValidator\EmailLexer::S_LOWERTHAN, \WappoVendor\Egulias\EmailValidator\EmailLexer::S_COLON, \WappoVendor\Egulias\EmailValidator\EmailLexer::S_SEMICOLON, \WappoVendor\Egulias\EmailValidator\EmailLexer::INVALID);
        if (\in_array($token['type'], $forbidden) && !$closingQuote) {
            throw new \WappoVendor\Egulias\EmailValidator\Exception\ExpectingATEXT();
        }
    }
}
