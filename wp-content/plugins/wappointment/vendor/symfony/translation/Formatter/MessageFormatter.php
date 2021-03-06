<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WappoVendor\Symfony\Component\Translation\Formatter;

use WappoVendor\Symfony\Component\Translation\MessageSelector;
/**
 * @author Abdellatif Ait boudad <a.aitboudad@gmail.com>
 */
class MessageFormatter implements \WappoVendor\Symfony\Component\Translation\Formatter\MessageFormatterInterface, \WappoVendor\Symfony\Component\Translation\Formatter\ChoiceMessageFormatterInterface
{
    private $selector;
    /**
     * @param MessageSelector|null $selector The message selector for pluralization
     */
    public function __construct(\WappoVendor\Symfony\Component\Translation\MessageSelector $selector = null)
    {
        $this->selector = $selector ?: new \WappoVendor\Symfony\Component\Translation\MessageSelector();
    }
    /**
     * {@inheritdoc}
     */
    public function format($message, $locale, array $parameters = [])
    {
        return \strtr($message, $parameters);
    }
    /**
     * {@inheritdoc}
     */
    public function choiceFormat($message, $number, $locale, array $parameters = [])
    {
        $parameters = \array_merge(['%count%' => $number], $parameters);
        return $this->format($this->selector->choose($message, (int) $number, $locale), $locale, $parameters);
    }
}
