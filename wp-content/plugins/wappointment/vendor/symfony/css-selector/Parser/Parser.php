<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WappoVendor\Symfony\Component\CssSelector\Parser;

use WappoVendor\Symfony\Component\CssSelector\Exception\SyntaxErrorException;
use WappoVendor\Symfony\Component\CssSelector\Node;
use WappoVendor\Symfony\Component\CssSelector\Parser\Tokenizer\Tokenizer;
/**
 * CSS selector parser.
 *
 * This component is a port of the Python cssselect library,
 * which is copyright Ian Bicking, @see https://github.com/SimonSapin/cssselect.
 *
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 *
 * @internal
 */
class Parser implements \WappoVendor\Symfony\Component\CssSelector\Parser\ParserInterface
{
    private $tokenizer;
    public function __construct(\WappoVendor\Symfony\Component\CssSelector\Parser\Tokenizer\Tokenizer $tokenizer = null)
    {
        $this->tokenizer = $tokenizer ?: new \WappoVendor\Symfony\Component\CssSelector\Parser\Tokenizer\Tokenizer();
    }
    /**
     * {@inheritdoc}
     */
    public function parse($source)
    {
        $reader = new \WappoVendor\Symfony\Component\CssSelector\Parser\Reader($source);
        $stream = $this->tokenizer->tokenize($reader);
        return $this->parseSelectorList($stream);
    }
    /**
     * Parses the arguments for ":nth-child()" and friends.
     *
     * @param Token[] $tokens
     *
     * @return array
     *
     * @throws SyntaxErrorException
     */
    public static function parseSeries(array $tokens)
    {
        foreach ($tokens as $token) {
            if ($token->isString()) {
                throw \WappoVendor\Symfony\Component\CssSelector\Exception\SyntaxErrorException::stringAsFunctionArgument();
            }
        }
        $joined = \trim(\implode('', \array_map(function (\WappoVendor\Symfony\Component\CssSelector\Parser\Token $token) {
            return $token->getValue();
        }, $tokens)));
        $int = function ($string) {
            if (!\is_numeric($string)) {
                throw \WappoVendor\Symfony\Component\CssSelector\Exception\SyntaxErrorException::stringAsFunctionArgument();
            }
            return (int) $string;
        };
        switch (true) {
            case 'odd' === $joined:
                return [2, 1];
            case 'even' === $joined:
                return [2, 0];
            case 'n' === $joined:
                return [1, 0];
            case false === \strpos($joined, 'n'):
                return [0, $int($joined)];
        }
        $split = \explode('n', $joined);
        $first = isset($split[0]) ? $split[0] : null;
        return [$first ? '-' === $first || '+' === $first ? $int($first . '1') : $int($first) : 1, isset($split[1]) && $split[1] ? $int($split[1]) : 0];
    }
    /**
     * Parses selector nodes.
     *
     * @return array
     */
    private function parseSelectorList(\WappoVendor\Symfony\Component\CssSelector\Parser\TokenStream $stream)
    {
        $stream->skipWhitespace();
        $selectors = [];
        while (true) {
            $selectors[] = $this->parserSelectorNode($stream);
            if ($stream->getPeek()->isDelimiter([','])) {
                $stream->getNext();
                $stream->skipWhitespace();
            } else {
                break;
            }
        }
        return $selectors;
    }
    /**
     * Parses next selector or combined node.
     *
     * @return Node\SelectorNode
     *
     * @throws SyntaxErrorException
     */
    private function parserSelectorNode(\WappoVendor\Symfony\Component\CssSelector\Parser\TokenStream $stream)
    {
        list($result, $pseudoElement) = $this->parseSimpleSelector($stream);
        while (true) {
            $stream->skipWhitespace();
            $peek = $stream->getPeek();
            if ($peek->isFileEnd() || $peek->isDelimiter([','])) {
                break;
            }
            if (null !== $pseudoElement) {
                throw \WappoVendor\Symfony\Component\CssSelector\Exception\SyntaxErrorException::pseudoElementFound($pseudoElement, 'not at the end of a selector');
            }
            if ($peek->isDelimiter(['+', '>', '~'])) {
                $combinator = $stream->getNext()->getValue();
                $stream->skipWhitespace();
            } else {
                $combinator = ' ';
            }
            list($nextSelector, $pseudoElement) = $this->parseSimpleSelector($stream);
            $result = new \WappoVendor\Symfony\Component\CssSelector\Node\CombinedSelectorNode($result, $combinator, $nextSelector);
        }
        return new \WappoVendor\Symfony\Component\CssSelector\Node\SelectorNode($result, $pseudoElement);
    }
    /**
     * Parses next simple node (hash, class, pseudo, negation).
     *
     * @param bool $insideNegation
     *
     * @return array
     *
     * @throws SyntaxErrorException
     */
    private function parseSimpleSelector(\WappoVendor\Symfony\Component\CssSelector\Parser\TokenStream $stream, $insideNegation = false)
    {
        $stream->skipWhitespace();
        $selectorStart = \count($stream->getUsed());
        $result = $this->parseElementNode($stream);
        $pseudoElement = null;
        while (true) {
            $peek = $stream->getPeek();
            if ($peek->isWhitespace() || $peek->isFileEnd() || $peek->isDelimiter([',', '+', '>', '~']) || $insideNegation && $peek->isDelimiter([')'])) {
                break;
            }
            if (null !== $pseudoElement) {
                throw \WappoVendor\Symfony\Component\CssSelector\Exception\SyntaxErrorException::pseudoElementFound($pseudoElement, 'not at the end of a selector');
            }
            if ($peek->isHash()) {
                $result = new \WappoVendor\Symfony\Component\CssSelector\Node\HashNode($result, $stream->getNext()->getValue());
            } elseif ($peek->isDelimiter(['.'])) {
                $stream->getNext();
                $result = new \WappoVendor\Symfony\Component\CssSelector\Node\ClassNode($result, $stream->getNextIdentifier());
            } elseif ($peek->isDelimiter(['['])) {
                $stream->getNext();
                $result = $this->parseAttributeNode($result, $stream);
            } elseif ($peek->isDelimiter([':'])) {
                $stream->getNext();
                if ($stream->getPeek()->isDelimiter([':'])) {
                    $stream->getNext();
                    $pseudoElement = $stream->getNextIdentifier();
                    continue;
                }
                $identifier = $stream->getNextIdentifier();
                if (\in_array(\strtolower($identifier), ['first-line', 'first-letter', 'before', 'after'])) {
                    // Special case: CSS 2.1 pseudo-elements can have a single ':'.
                    // Any new pseudo-element must have two.
                    $pseudoElement = $identifier;
                    continue;
                }
                if (!$stream->getPeek()->isDelimiter(['('])) {
                    $result = new \WappoVendor\Symfony\Component\CssSelector\Node\PseudoNode($result, $identifier);
                    continue;
                }
                $stream->getNext();
                $stream->skipWhitespace();
                if ('not' === \strtolower($identifier)) {
                    if ($insideNegation) {
                        throw \WappoVendor\Symfony\Component\CssSelector\Exception\SyntaxErrorException::nestedNot();
                    }
                    list($argument, $argumentPseudoElement) = $this->parseSimpleSelector($stream, true);
                    $next = $stream->getNext();
                    if (null !== $argumentPseudoElement) {
                        throw \WappoVendor\Symfony\Component\CssSelector\Exception\SyntaxErrorException::pseudoElementFound($argumentPseudoElement, 'inside ::not()');
                    }
                    if (!$next->isDelimiter([')'])) {
                        throw \WappoVendor\Symfony\Component\CssSelector\Exception\SyntaxErrorException::unexpectedToken('")"', $next);
                    }
                    $result = new \WappoVendor\Symfony\Component\CssSelector\Node\NegationNode($result, $argument);
                } else {
                    $arguments = [];
                    $next = null;
                    while (true) {
                        $stream->skipWhitespace();
                        $next = $stream->getNext();
                        if ($next->isIdentifier() || $next->isString() || $next->isNumber() || $next->isDelimiter(['+', '-'])) {
                            $arguments[] = $next;
                        } elseif ($next->isDelimiter([')'])) {
                            break;
                        } else {
                            throw \WappoVendor\Symfony\Component\CssSelector\Exception\SyntaxErrorException::unexpectedToken('an argument', $next);
                        }
                    }
                    if (empty($arguments)) {
                        throw \WappoVendor\Symfony\Component\CssSelector\Exception\SyntaxErrorException::unexpectedToken('at least one argument', $next);
                    }
                    $result = new \WappoVendor\Symfony\Component\CssSelector\Node\FunctionNode($result, $identifier, $arguments);
                }
            } else {
                throw \WappoVendor\Symfony\Component\CssSelector\Exception\SyntaxErrorException::unexpectedToken('selector', $peek);
            }
        }
        if (\count($stream->getUsed()) === $selectorStart) {
            throw \WappoVendor\Symfony\Component\CssSelector\Exception\SyntaxErrorException::unexpectedToken('selector', $stream->getPeek());
        }
        return [$result, $pseudoElement];
    }
    /**
     * Parses next element node.
     *
     * @return Node\ElementNode
     */
    private function parseElementNode(\WappoVendor\Symfony\Component\CssSelector\Parser\TokenStream $stream)
    {
        $peek = $stream->getPeek();
        if ($peek->isIdentifier() || $peek->isDelimiter(['*'])) {
            if ($peek->isIdentifier()) {
                $namespace = $stream->getNext()->getValue();
            } else {
                $stream->getNext();
                $namespace = null;
            }
            if ($stream->getPeek()->isDelimiter(['|'])) {
                $stream->getNext();
                $element = $stream->getNextIdentifierOrStar();
            } else {
                $element = $namespace;
                $namespace = null;
            }
        } else {
            $element = $namespace = null;
        }
        return new \WappoVendor\Symfony\Component\CssSelector\Node\ElementNode($namespace, $element);
    }
    /**
     * Parses next attribute node.
     *
     * @return Node\AttributeNode
     *
     * @throws SyntaxErrorException
     */
    private function parseAttributeNode(\WappoVendor\Symfony\Component\CssSelector\Node\NodeInterface $selector, \WappoVendor\Symfony\Component\CssSelector\Parser\TokenStream $stream)
    {
        $stream->skipWhitespace();
        $attribute = $stream->getNextIdentifierOrStar();
        if (null === $attribute && !$stream->getPeek()->isDelimiter(['|'])) {
            throw \WappoVendor\Symfony\Component\CssSelector\Exception\SyntaxErrorException::unexpectedToken('"|"', $stream->getPeek());
        }
        if ($stream->getPeek()->isDelimiter(['|'])) {
            $stream->getNext();
            if ($stream->getPeek()->isDelimiter(['='])) {
                $namespace = null;
                $stream->getNext();
                $operator = '|=';
            } else {
                $namespace = $attribute;
                $attribute = $stream->getNextIdentifier();
                $operator = null;
            }
        } else {
            $namespace = $operator = null;
        }
        if (null === $operator) {
            $stream->skipWhitespace();
            $next = $stream->getNext();
            if ($next->isDelimiter([']'])) {
                return new \WappoVendor\Symfony\Component\CssSelector\Node\AttributeNode($selector, $namespace, $attribute, 'exists', null);
            } elseif ($next->isDelimiter(['='])) {
                $operator = '=';
            } elseif ($next->isDelimiter(['^', '$', '*', '~', '|', '!']) && $stream->getPeek()->isDelimiter(['='])) {
                $operator = $next->getValue() . '=';
                $stream->getNext();
            } else {
                throw \WappoVendor\Symfony\Component\CssSelector\Exception\SyntaxErrorException::unexpectedToken('operator', $next);
            }
        }
        $stream->skipWhitespace();
        $value = $stream->getNext();
        if ($value->isNumber()) {
            // if the value is a number, it's casted into a string
            $value = new \WappoVendor\Symfony\Component\CssSelector\Parser\Token(\WappoVendor\Symfony\Component\CssSelector\Parser\Token::TYPE_STRING, (string) $value->getValue(), $value->getPosition());
        }
        if (!($value->isIdentifier() || $value->isString())) {
            throw \WappoVendor\Symfony\Component\CssSelector\Exception\SyntaxErrorException::unexpectedToken('string or identifier', $value);
        }
        $stream->skipWhitespace();
        $next = $stream->getNext();
        if (!$next->isDelimiter([']'])) {
            throw \WappoVendor\Symfony\Component\CssSelector\Exception\SyntaxErrorException::unexpectedToken('"]"', $next);
        }
        return new \WappoVendor\Symfony\Component\CssSelector\Node\AttributeNode($selector, $namespace, $attribute, $operator, $value->getValue());
    }
}
