<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WappoVendor\Symfony\Component\CssSelector\XPath\Extension;

use WappoVendor\Symfony\Component\CssSelector\Node;
use WappoVendor\Symfony\Component\CssSelector\XPath\Translator;
use WappoVendor\Symfony\Component\CssSelector\XPath\XPathExpr;
/**
 * XPath expression translator node extension.
 *
 * This component is a port of the Python cssselect library,
 * which is copyright Ian Bicking, @see https://github.com/SimonSapin/cssselect.
 *
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 *
 * @internal
 */
class NodeExtension extends \WappoVendor\Symfony\Component\CssSelector\XPath\Extension\AbstractExtension
{
    const ELEMENT_NAME_IN_LOWER_CASE = 1;
    const ATTRIBUTE_NAME_IN_LOWER_CASE = 2;
    const ATTRIBUTE_VALUE_IN_LOWER_CASE = 4;
    private $flags;
    /**
     * @param int $flags
     */
    public function __construct($flags = 0)
    {
        $this->flags = $flags;
    }
    /**
     * @param int  $flag
     * @param bool $on
     *
     * @return $this
     */
    public function setFlag($flag, $on)
    {
        if ($on && !$this->hasFlag($flag)) {
            $this->flags += $flag;
        }
        if (!$on && $this->hasFlag($flag)) {
            $this->flags -= $flag;
        }
        return $this;
    }
    /**
     * @param int $flag
     *
     * @return bool
     */
    public function hasFlag($flag)
    {
        return (bool) ($this->flags & $flag);
    }
    /**
     * {@inheritdoc}
     */
    public function getNodeTranslators()
    {
        return ['Selector' => [$this, 'translateSelector'], 'CombinedSelector' => [$this, 'translateCombinedSelector'], 'Negation' => [$this, 'translateNegation'], 'Function' => [$this, 'translateFunction'], 'Pseudo' => [$this, 'translatePseudo'], 'Attribute' => [$this, 'translateAttribute'], 'Class' => [$this, 'translateClass'], 'Hash' => [$this, 'translateHash'], 'Element' => [$this, 'translateElement']];
    }
    /**
     * @return XPathExpr
     */
    public function translateSelector(\WappoVendor\Symfony\Component\CssSelector\Node\SelectorNode $node, \WappoVendor\Symfony\Component\CssSelector\XPath\Translator $translator)
    {
        return $translator->nodeToXPath($node->getTree());
    }
    /**
     * @return XPathExpr
     */
    public function translateCombinedSelector(\WappoVendor\Symfony\Component\CssSelector\Node\CombinedSelectorNode $node, \WappoVendor\Symfony\Component\CssSelector\XPath\Translator $translator)
    {
        return $translator->addCombination($node->getCombinator(), $node->getSelector(), $node->getSubSelector());
    }
    /**
     * @return XPathExpr
     */
    public function translateNegation(\WappoVendor\Symfony\Component\CssSelector\Node\NegationNode $node, \WappoVendor\Symfony\Component\CssSelector\XPath\Translator $translator)
    {
        $xpath = $translator->nodeToXPath($node->getSelector());
        $subXpath = $translator->nodeToXPath($node->getSubSelector());
        $subXpath->addNameTest();
        if ($subXpath->getCondition()) {
            return $xpath->addCondition(\sprintf('not(%s)', $subXpath->getCondition()));
        }
        return $xpath->addCondition('0');
    }
    /**
     * @return XPathExpr
     */
    public function translateFunction(\WappoVendor\Symfony\Component\CssSelector\Node\FunctionNode $node, \WappoVendor\Symfony\Component\CssSelector\XPath\Translator $translator)
    {
        $xpath = $translator->nodeToXPath($node->getSelector());
        return $translator->addFunction($xpath, $node);
    }
    /**
     * @return XPathExpr
     */
    public function translatePseudo(\WappoVendor\Symfony\Component\CssSelector\Node\PseudoNode $node, \WappoVendor\Symfony\Component\CssSelector\XPath\Translator $translator)
    {
        $xpath = $translator->nodeToXPath($node->getSelector());
        return $translator->addPseudoClass($xpath, $node->getIdentifier());
    }
    /**
     * @return XPathExpr
     */
    public function translateAttribute(\WappoVendor\Symfony\Component\CssSelector\Node\AttributeNode $node, \WappoVendor\Symfony\Component\CssSelector\XPath\Translator $translator)
    {
        $name = $node->getAttribute();
        $safe = $this->isSafeName($name);
        if ($this->hasFlag(self::ATTRIBUTE_NAME_IN_LOWER_CASE)) {
            $name = \strtolower($name);
        }
        if ($node->getNamespace()) {
            $name = \sprintf('%s:%s', $node->getNamespace(), $name);
            $safe = $safe && $this->isSafeName($node->getNamespace());
        }
        $attribute = $safe ? '@' . $name : \sprintf('attribute::*[name() = %s]', \WappoVendor\Symfony\Component\CssSelector\XPath\Translator::getXpathLiteral($name));
        $value = $node->getValue();
        $xpath = $translator->nodeToXPath($node->getSelector());
        if ($this->hasFlag(self::ATTRIBUTE_VALUE_IN_LOWER_CASE)) {
            $value = \strtolower($value);
        }
        return $translator->addAttributeMatching($xpath, $node->getOperator(), $attribute, $value);
    }
    /**
     * @return XPathExpr
     */
    public function translateClass(\WappoVendor\Symfony\Component\CssSelector\Node\ClassNode $node, \WappoVendor\Symfony\Component\CssSelector\XPath\Translator $translator)
    {
        $xpath = $translator->nodeToXPath($node->getSelector());
        return $translator->addAttributeMatching($xpath, '~=', '@class', $node->getName());
    }
    /**
     * @return XPathExpr
     */
    public function translateHash(\WappoVendor\Symfony\Component\CssSelector\Node\HashNode $node, \WappoVendor\Symfony\Component\CssSelector\XPath\Translator $translator)
    {
        $xpath = $translator->nodeToXPath($node->getSelector());
        return $translator->addAttributeMatching($xpath, '=', '@id', $node->getId());
    }
    /**
     * @return XPathExpr
     */
    public function translateElement(\WappoVendor\Symfony\Component\CssSelector\Node\ElementNode $node)
    {
        $element = $node->getElement();
        if ($this->hasFlag(self::ELEMENT_NAME_IN_LOWER_CASE)) {
            $element = \strtolower($element);
        }
        if ($element) {
            $safe = $this->isSafeName($element);
        } else {
            $element = '*';
            $safe = true;
        }
        if ($node->getNamespace()) {
            $element = \sprintf('%s:%s', $node->getNamespace(), $element);
            $safe = $safe && $this->isSafeName($node->getNamespace());
        }
        $xpath = new \WappoVendor\Symfony\Component\CssSelector\XPath\XPathExpr('', $element);
        if (!$safe) {
            $xpath->addNameTest();
        }
        return $xpath;
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'node';
    }
    /**
     * Tests if given name is safe.
     *
     * @param string $name
     *
     * @return bool
     */
    private function isSafeName($name)
    {
        return 0 < \preg_match('~^[a-zA-Z_][a-zA-Z0-9_.-]*$~', $name);
    }
}
