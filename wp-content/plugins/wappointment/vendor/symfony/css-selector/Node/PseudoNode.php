<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WappoVendor\Symfony\Component\CssSelector\Node;

/**
 * Represents a "<selector>:<identifier>" node.
 *
 * This component is a port of the Python cssselect library,
 * which is copyright Ian Bicking, @see https://github.com/SimonSapin/cssselect.
 *
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 *
 * @internal
 */
class PseudoNode extends \WappoVendor\Symfony\Component\CssSelector\Node\AbstractNode
{
    private $selector;
    private $identifier;
    /**
     * @param string $identifier
     */
    public function __construct(\WappoVendor\Symfony\Component\CssSelector\Node\NodeInterface $selector, $identifier)
    {
        $this->selector = $selector;
        $this->identifier = \strtolower($identifier);
    }
    /**
     * @return NodeInterface
     */
    public function getSelector()
    {
        return $this->selector;
    }
    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }
    /**
     * {@inheritdoc}
     */
    public function getSpecificity()
    {
        return $this->selector->getSpecificity()->plus(new \WappoVendor\Symfony\Component\CssSelector\Node\Specificity(0, 1, 0));
    }
    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return \sprintf('%s[%s:%s]', $this->getNodeName(), $this->selector, $this->identifier);
    }
}
