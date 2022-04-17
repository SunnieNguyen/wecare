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

use WappoVendor\Symfony\Component\CssSelector\XPath\XPathExpr;
/**
 * XPath expression translator combination extension.
 *
 * This component is a port of the Python cssselect library,
 * which is copyright Ian Bicking, @see https://github.com/SimonSapin/cssselect.
 *
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 *
 * @internal
 */
class CombinationExtension extends \WappoVendor\Symfony\Component\CssSelector\XPath\Extension\AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getCombinationTranslators()
    {
        return [' ' => [$this, 'translateDescendant'], '>' => [$this, 'translateChild'], '+' => [$this, 'translateDirectAdjacent'], '~' => [$this, 'translateIndirectAdjacent']];
    }
    /**
     * @return XPathExpr
     */
    public function translateDescendant(\WappoVendor\Symfony\Component\CssSelector\XPath\XPathExpr $xpath, \WappoVendor\Symfony\Component\CssSelector\XPath\XPathExpr $combinedXpath)
    {
        return $xpath->join('/descendant-or-self::*/', $combinedXpath);
    }
    /**
     * @return XPathExpr
     */
    public function translateChild(\WappoVendor\Symfony\Component\CssSelector\XPath\XPathExpr $xpath, \WappoVendor\Symfony\Component\CssSelector\XPath\XPathExpr $combinedXpath)
    {
        return $xpath->join('/', $combinedXpath);
    }
    /**
     * @return XPathExpr
     */
    public function translateDirectAdjacent(\WappoVendor\Symfony\Component\CssSelector\XPath\XPathExpr $xpath, \WappoVendor\Symfony\Component\CssSelector\XPath\XPathExpr $combinedXpath)
    {
        return $xpath->join('/following-sibling::', $combinedXpath)->addNameTest()->addCondition('position() = 1');
    }
    /**
     * @return XPathExpr
     */
    public function translateIndirectAdjacent(\WappoVendor\Symfony\Component\CssSelector\XPath\XPathExpr $xpath, \WappoVendor\Symfony\Component\CssSelector\XPath\XPathExpr $combinedXpath)
    {
        return $xpath->join('/following-sibling::', $combinedXpath);
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'combination';
    }
}
