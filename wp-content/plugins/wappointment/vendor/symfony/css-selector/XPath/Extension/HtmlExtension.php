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

use WappoVendor\Symfony\Component\CssSelector\Exception\ExpressionErrorException;
use WappoVendor\Symfony\Component\CssSelector\Node\FunctionNode;
use WappoVendor\Symfony\Component\CssSelector\XPath\Translator;
use WappoVendor\Symfony\Component\CssSelector\XPath\XPathExpr;
/**
 * XPath expression translator HTML extension.
 *
 * This component is a port of the Python cssselect library,
 * which is copyright Ian Bicking, @see https://github.com/SimonSapin/cssselect.
 *
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 *
 * @internal
 */
class HtmlExtension extends \WappoVendor\Symfony\Component\CssSelector\XPath\Extension\AbstractExtension
{
    public function __construct(\WappoVendor\Symfony\Component\CssSelector\XPath\Translator $translator)
    {
        $translator->getExtension('node')->setFlag(\WappoVendor\Symfony\Component\CssSelector\XPath\Extension\NodeExtension::ELEMENT_NAME_IN_LOWER_CASE, true)->setFlag(\WappoVendor\Symfony\Component\CssSelector\XPath\Extension\NodeExtension::ATTRIBUTE_NAME_IN_LOWER_CASE, true);
    }
    /**
     * {@inheritdoc}
     */
    public function getPseudoClassTranslators()
    {
        return ['checked' => [$this, 'translateChecked'], 'link' => [$this, 'translateLink'], 'disabled' => [$this, 'translateDisabled'], 'enabled' => [$this, 'translateEnabled'], 'selected' => [$this, 'translateSelected'], 'invalid' => [$this, 'translateInvalid'], 'hover' => [$this, 'translateHover'], 'visited' => [$this, 'translateVisited']];
    }
    /**
     * {@inheritdoc}
     */
    public function getFunctionTranslators()
    {
        return ['lang' => [$this, 'translateLang']];
    }
    /**
     * @return XPathExpr
     */
    public function translateChecked(\WappoVendor\Symfony\Component\CssSelector\XPath\XPathExpr $xpath)
    {
        return $xpath->addCondition('(@checked ' . "and (name(.) = 'input' or name(.) = 'command')" . "and (@type = 'checkbox' or @type = 'radio'))");
    }
    /**
     * @return XPathExpr
     */
    public function translateLink(\WappoVendor\Symfony\Component\CssSelector\XPath\XPathExpr $xpath)
    {
        return $xpath->addCondition("@href and (name(.) = 'a' or name(.) = 'link' or name(.) = 'area')");
    }
    /**
     * @return XPathExpr
     */
    public function translateDisabled(\WappoVendor\Symfony\Component\CssSelector\XPath\XPathExpr $xpath)
    {
        return $xpath->addCondition('(' . '@disabled and' . '(' . "(name(.) = 'input' and @type != 'hidden')" . " or name(.) = 'button'" . " or name(.) = 'select'" . " or name(.) = 'textarea'" . " or name(.) = 'command'" . " or name(.) = 'fieldset'" . " or name(.) = 'optgroup'" . " or name(.) = 'option'" . ')' . ') or (' . "(name(.) = 'input' and @type != 'hidden')" . " or name(.) = 'button'" . " or name(.) = 'select'" . " or name(.) = 'textarea'" . ')' . ' and ancestor::fieldset[@disabled]');
        // todo: in the second half, add "and is not a descendant of that fieldset element's first legend element child, if any."
    }
    /**
     * @return XPathExpr
     */
    public function translateEnabled(\WappoVendor\Symfony\Component\CssSelector\XPath\XPathExpr $xpath)
    {
        return $xpath->addCondition('(' . '@href and (' . "name(.) = 'a'" . " or name(.) = 'link'" . " or name(.) = 'area'" . ')' . ') or (' . '(' . "name(.) = 'command'" . " or name(.) = 'fieldset'" . " or name(.) = 'optgroup'" . ')' . ' and not(@disabled)' . ') or (' . '(' . "(name(.) = 'input' and @type != 'hidden')" . " or name(.) = 'button'" . " or name(.) = 'select'" . " or name(.) = 'textarea'" . " or name(.) = 'keygen'" . ')' . ' and not (@disabled or ancestor::fieldset[@disabled])' . ') or (' . "name(.) = 'option' and not(" . '@disabled or ancestor::optgroup[@disabled]' . ')' . ')');
    }
    /**
     * @return XPathExpr
     *
     * @throws ExpressionErrorException
     */
    public function translateLang(\WappoVendor\Symfony\Component\CssSelector\XPath\XPathExpr $xpath, \WappoVendor\Symfony\Component\CssSelector\Node\FunctionNode $function)
    {
        $arguments = $function->getArguments();
        foreach ($arguments as $token) {
            if (!($token->isString() || $token->isIdentifier())) {
                throw new \WappoVendor\Symfony\Component\CssSelector\Exception\ExpressionErrorException('Expected a single string or identifier for :lang(), got ' . \implode(', ', $arguments));
            }
        }
        return $xpath->addCondition(\sprintf('ancestor-or-self::*[@lang][1][starts-with(concat(' . "translate(@%s, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz'), '-')" . ', %s)]', 'lang', \WappoVendor\Symfony\Component\CssSelector\XPath\Translator::getXpathLiteral(\strtolower($arguments[0]->getValue()) . '-')));
    }
    /**
     * @return XPathExpr
     */
    public function translateSelected(\WappoVendor\Symfony\Component\CssSelector\XPath\XPathExpr $xpath)
    {
        return $xpath->addCondition("(@selected and name(.) = 'option')");
    }
    /**
     * @return XPathExpr
     */
    public function translateInvalid(\WappoVendor\Symfony\Component\CssSelector\XPath\XPathExpr $xpath)
    {
        return $xpath->addCondition('0');
    }
    /**
     * @return XPathExpr
     */
    public function translateHover(\WappoVendor\Symfony\Component\CssSelector\XPath\XPathExpr $xpath)
    {
        return $xpath->addCondition('0');
    }
    /**
     * @return XPathExpr
     */
    public function translateVisited(\WappoVendor\Symfony\Component\CssSelector\XPath\XPathExpr $xpath)
    {
        return $xpath->addCondition('0');
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'html';
    }
}
