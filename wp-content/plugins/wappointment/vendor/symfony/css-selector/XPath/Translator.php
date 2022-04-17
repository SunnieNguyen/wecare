<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WappoVendor\Symfony\Component\CssSelector\XPath;

use WappoVendor\Symfony\Component\CssSelector\Exception\ExpressionErrorException;
use WappoVendor\Symfony\Component\CssSelector\Node\FunctionNode;
use WappoVendor\Symfony\Component\CssSelector\Node\NodeInterface;
use WappoVendor\Symfony\Component\CssSelector\Node\SelectorNode;
use WappoVendor\Symfony\Component\CssSelector\Parser\Parser;
use WappoVendor\Symfony\Component\CssSelector\Parser\ParserInterface;
/**
 * XPath expression translator interface.
 *
 * This component is a port of the Python cssselect library,
 * which is copyright Ian Bicking, @see https://github.com/SimonSapin/cssselect.
 *
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 *
 * @internal
 */
class Translator implements \WappoVendor\Symfony\Component\CssSelector\XPath\TranslatorInterface
{
    private $mainParser;
    /**
     * @var ParserInterface[]
     */
    private $shortcutParsers = [];
    /**
     * @var Extension\ExtensionInterface[]
     */
    private $extensions = [];
    private $nodeTranslators = [];
    private $combinationTranslators = [];
    private $functionTranslators = [];
    private $pseudoClassTranslators = [];
    private $attributeMatchingTranslators = [];
    public function __construct(\WappoVendor\Symfony\Component\CssSelector\Parser\ParserInterface $parser = null)
    {
        $this->mainParser = $parser ?: new \WappoVendor\Symfony\Component\CssSelector\Parser\Parser();
        $this->registerExtension(new \WappoVendor\Symfony\Component\CssSelector\XPath\Extension\NodeExtension())->registerExtension(new \WappoVendor\Symfony\Component\CssSelector\XPath\Extension\CombinationExtension())->registerExtension(new \WappoVendor\Symfony\Component\CssSelector\XPath\Extension\FunctionExtension())->registerExtension(new \WappoVendor\Symfony\Component\CssSelector\XPath\Extension\PseudoClassExtension())->registerExtension(new \WappoVendor\Symfony\Component\CssSelector\XPath\Extension\AttributeMatchingExtension());
    }
    /**
     * @param string $element
     *
     * @return string
     */
    public static function getXpathLiteral($element)
    {
        if (false === \strpos($element, "'")) {
            return "'" . $element . "'";
        }
        if (false === \strpos($element, '"')) {
            return '"' . $element . '"';
        }
        $string = $element;
        $parts = [];
        while (true) {
            if (false !== ($pos = \strpos($string, "'"))) {
                $parts[] = \sprintf("'%s'", \substr($string, 0, $pos));
                $parts[] = "\"'\"";
                $string = \substr($string, $pos + 1);
            } else {
                $parts[] = "'{$string}'";
                break;
            }
        }
        return \sprintf('concat(%s)', \implode(', ', $parts));
    }
    /**
     * {@inheritdoc}
     */
    public function cssToXPath($cssExpr, $prefix = 'descendant-or-self::')
    {
        $selectors = $this->parseSelectors($cssExpr);
        /** @var SelectorNode $selector */
        foreach ($selectors as $index => $selector) {
            if (null !== $selector->getPseudoElement()) {
                throw new \WappoVendor\Symfony\Component\CssSelector\Exception\ExpressionErrorException('Pseudo-elements are not supported.');
            }
            $selectors[$index] = $this->selectorToXPath($selector, $prefix);
        }
        return \implode(' | ', $selectors);
    }
    /**
     * {@inheritdoc}
     */
    public function selectorToXPath(\WappoVendor\Symfony\Component\CssSelector\Node\SelectorNode $selector, $prefix = 'descendant-or-self::')
    {
        return ($prefix ?: '') . $this->nodeToXPath($selector);
    }
    /**
     * Registers an extension.
     *
     * @return $this
     */
    public function registerExtension(\WappoVendor\Symfony\Component\CssSelector\XPath\Extension\ExtensionInterface $extension)
    {
        $this->extensions[$extension->getName()] = $extension;
        $this->nodeTranslators = \array_merge($this->nodeTranslators, $extension->getNodeTranslators());
        $this->combinationTranslators = \array_merge($this->combinationTranslators, $extension->getCombinationTranslators());
        $this->functionTranslators = \array_merge($this->functionTranslators, $extension->getFunctionTranslators());
        $this->pseudoClassTranslators = \array_merge($this->pseudoClassTranslators, $extension->getPseudoClassTranslators());
        $this->attributeMatchingTranslators = \array_merge($this->attributeMatchingTranslators, $extension->getAttributeMatchingTranslators());
        return $this;
    }
    /**
     * @param string $name
     *
     * @return Extension\ExtensionInterface
     *
     * @throws ExpressionErrorException
     */
    public function getExtension($name)
    {
        if (!isset($this->extensions[$name])) {
            throw new \WappoVendor\Symfony\Component\CssSelector\Exception\ExpressionErrorException(\sprintf('Extension "%s" not registered.', $name));
        }
        return $this->extensions[$name];
    }
    /**
     * Registers a shortcut parser.
     *
     * @return $this
     */
    public function registerParserShortcut(\WappoVendor\Symfony\Component\CssSelector\Parser\ParserInterface $shortcut)
    {
        $this->shortcutParsers[] = $shortcut;
        return $this;
    }
    /**
     * @return XPathExpr
     *
     * @throws ExpressionErrorException
     */
    public function nodeToXPath(\WappoVendor\Symfony\Component\CssSelector\Node\NodeInterface $node)
    {
        if (!isset($this->nodeTranslators[$node->getNodeName()])) {
            throw new \WappoVendor\Symfony\Component\CssSelector\Exception\ExpressionErrorException(\sprintf('Node "%s" not supported.', $node->getNodeName()));
        }
        return \call_user_func($this->nodeTranslators[$node->getNodeName()], $node, $this);
    }
    /**
     * @param string $combiner
     *
     * @return XPathExpr
     *
     * @throws ExpressionErrorException
     */
    public function addCombination($combiner, \WappoVendor\Symfony\Component\CssSelector\Node\NodeInterface $xpath, \WappoVendor\Symfony\Component\CssSelector\Node\NodeInterface $combinedXpath)
    {
        if (!isset($this->combinationTranslators[$combiner])) {
            throw new \WappoVendor\Symfony\Component\CssSelector\Exception\ExpressionErrorException(\sprintf('Combiner "%s" not supported.', $combiner));
        }
        return \call_user_func($this->combinationTranslators[$combiner], $this->nodeToXPath($xpath), $this->nodeToXPath($combinedXpath));
    }
    /**
     * @return XPathExpr
     *
     * @throws ExpressionErrorException
     */
    public function addFunction(\WappoVendor\Symfony\Component\CssSelector\XPath\XPathExpr $xpath, \WappoVendor\Symfony\Component\CssSelector\Node\FunctionNode $function)
    {
        if (!isset($this->functionTranslators[$function->getName()])) {
            throw new \WappoVendor\Symfony\Component\CssSelector\Exception\ExpressionErrorException(\sprintf('Function "%s" not supported.', $function->getName()));
        }
        return \call_user_func($this->functionTranslators[$function->getName()], $xpath, $function);
    }
    /**
     * @param string $pseudoClass
     *
     * @return XPathExpr
     *
     * @throws ExpressionErrorException
     */
    public function addPseudoClass(\WappoVendor\Symfony\Component\CssSelector\XPath\XPathExpr $xpath, $pseudoClass)
    {
        if (!isset($this->pseudoClassTranslators[$pseudoClass])) {
            throw new \WappoVendor\Symfony\Component\CssSelector\Exception\ExpressionErrorException(\sprintf('Pseudo-class "%s" not supported.', $pseudoClass));
        }
        return \call_user_func($this->pseudoClassTranslators[$pseudoClass], $xpath);
    }
    /**
     * @param string $operator
     * @param string $attribute
     * @param string $value
     *
     * @return XPathExpr
     *
     * @throws ExpressionErrorException
     */
    public function addAttributeMatching(\WappoVendor\Symfony\Component\CssSelector\XPath\XPathExpr $xpath, $operator, $attribute, $value)
    {
        if (!isset($this->attributeMatchingTranslators[$operator])) {
            throw new \WappoVendor\Symfony\Component\CssSelector\Exception\ExpressionErrorException(\sprintf('Attribute matcher operator "%s" not supported.', $operator));
        }
        return \call_user_func($this->attributeMatchingTranslators[$operator], $xpath, $attribute, $value);
    }
    /**
     * @param string $css
     *
     * @return SelectorNode[]
     */
    private function parseSelectors($css)
    {
        foreach ($this->shortcutParsers as $shortcut) {
            $tokens = $shortcut->parse($css);
            if (!empty($tokens)) {
                return $tokens;
            }
        }
        return $this->mainParser->parse($css);
    }
}
