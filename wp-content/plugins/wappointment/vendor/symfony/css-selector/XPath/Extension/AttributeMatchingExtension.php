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

use WappoVendor\Symfony\Component\CssSelector\XPath\Translator;
use WappoVendor\Symfony\Component\CssSelector\XPath\XPathExpr;
/**
 * XPath expression translator attribute extension.
 *
 * This component is a port of the Python cssselect library,
 * which is copyright Ian Bicking, @see https://github.com/SimonSapin/cssselect.
 *
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 *
 * @internal
 */
class AttributeMatchingExtension extends \WappoVendor\Symfony\Component\CssSelector\XPath\Extension\AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getAttributeMatchingTranslators()
    {
        return ['exists' => [$this, 'translateExists'], '=' => [$this, 'translateEquals'], '~=' => [$this, 'translateIncludes'], '|=' => [$this, 'translateDashMatch'], '^=' => [$this, 'translatePrefixMatch'], '$=' => [$this, 'translateSuffixMatch'], '*=' => [$this, 'translateSubstringMatch'], '!=' => [$this, 'translateDifferent']];
    }
    /**
     * @param string $attribute
     * @param string $value
     *
     * @return XPathExpr
     */
    public function translateExists(\WappoVendor\Symfony\Component\CssSelector\XPath\XPathExpr $xpath, $attribute, $value)
    {
        return $xpath->addCondition($attribute);
    }
    /**
     * @param string $attribute
     * @param string $value
     *
     * @return XPathExpr
     */
    public function translateEquals(\WappoVendor\Symfony\Component\CssSelector\XPath\XPathExpr $xpath, $attribute, $value)
    {
        return $xpath->addCondition(\sprintf('%s = %s', $attribute, \WappoVendor\Symfony\Component\CssSelector\XPath\Translator::getXpathLiteral($value)));
    }
    /**
     * @param string $attribute
     * @param string $value
     *
     * @return XPathExpr
     */
    public function translateIncludes(\WappoVendor\Symfony\Component\CssSelector\XPath\XPathExpr $xpath, $attribute, $value)
    {
        return $xpath->addCondition($value ? \sprintf('%1$s and contains(concat(\' \', normalize-space(%1$s), \' \'), %2$s)', $attribute, \WappoVendor\Symfony\Component\CssSelector\XPath\Translator::getXpathLiteral(' ' . $value . ' ')) : '0');
    }
    /**
     * @param string $attribute
     * @param string $value
     *
     * @return XPathExpr
     */
    public function translateDashMatch(\WappoVendor\Symfony\Component\CssSelector\XPath\XPathExpr $xpath, $attribute, $value)
    {
        return $xpath->addCondition(\sprintf('%1$s and (%1$s = %2$s or starts-with(%1$s, %3$s))', $attribute, \WappoVendor\Symfony\Component\CssSelector\XPath\Translator::getXpathLiteral($value), \WappoVendor\Symfony\Component\CssSelector\XPath\Translator::getXpathLiteral($value . '-')));
    }
    /**
     * @param string $attribute
     * @param string $value
     *
     * @return XPathExpr
     */
    public function translatePrefixMatch(\WappoVendor\Symfony\Component\CssSelector\XPath\XPathExpr $xpath, $attribute, $value)
    {
        return $xpath->addCondition($value ? \sprintf('%1$s and starts-with(%1$s, %2$s)', $attribute, \WappoVendor\Symfony\Component\CssSelector\XPath\Translator::getXpathLiteral($value)) : '0');
    }
    /**
     * @param string $attribute
     * @param string $value
     *
     * @return XPathExpr
     */
    public function translateSuffixMatch(\WappoVendor\Symfony\Component\CssSelector\XPath\XPathExpr $xpath, $attribute, $value)
    {
        return $xpath->addCondition($value ? \sprintf('%1$s and substring(%1$s, string-length(%1$s)-%2$s) = %3$s', $attribute, \strlen($value) - 1, \WappoVendor\Symfony\Component\CssSelector\XPath\Translator::getXpathLiteral($value)) : '0');
    }
    /**
     * @param string $attribute
     * @param string $value
     *
     * @return XPathExpr
     */
    public function translateSubstringMatch(\WappoVendor\Symfony\Component\CssSelector\XPath\XPathExpr $xpath, $attribute, $value)
    {
        return $xpath->addCondition($value ? \sprintf('%1$s and contains(%1$s, %2$s)', $attribute, \WappoVendor\Symfony\Component\CssSelector\XPath\Translator::getXpathLiteral($value)) : '0');
    }
    /**
     * @param string $attribute
     * @param string $value
     *
     * @return XPathExpr
     */
    public function translateDifferent(\WappoVendor\Symfony\Component\CssSelector\XPath\XPathExpr $xpath, $attribute, $value)
    {
        return $xpath->addCondition(\sprintf($value ? 'not(%1$s) or %1$s != %2$s' : '%s != %s', $attribute, \WappoVendor\Symfony\Component\CssSelector\XPath\Translator::getXpathLiteral($value)));
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'attribute-matching';
    }
}
