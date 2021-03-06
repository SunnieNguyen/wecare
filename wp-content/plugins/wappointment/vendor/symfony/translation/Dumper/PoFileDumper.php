<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WappoVendor\Symfony\Component\Translation\Dumper;

use WappoVendor\Symfony\Component\Translation\MessageCatalogue;
/**
 * PoFileDumper generates a gettext formatted string representation of a message catalogue.
 *
 * @author Stealth35
 */
class PoFileDumper extends \WappoVendor\Symfony\Component\Translation\Dumper\FileDumper
{
    /**
     * {@inheritdoc}
     */
    public function formatCatalogue(\WappoVendor\Symfony\Component\Translation\MessageCatalogue $messages, $domain, array $options = [])
    {
        $output = 'msgid ""' . "\n";
        $output .= 'msgstr ""' . "\n";
        $output .= '"Content-Type: text/plain; charset=UTF-8\\n"' . "\n";
        $output .= '"Content-Transfer-Encoding: 8bit\\n"' . "\n";
        $output .= '"Language: ' . $messages->getLocale() . '\\n"' . "\n";
        $output .= "\n";
        $newLine = false;
        foreach ($messages->all($domain) as $source => $target) {
            if ($newLine) {
                $output .= "\n";
            } else {
                $newLine = true;
            }
            $output .= \sprintf('msgid "%s"' . "\n", $this->escape($source));
            $output .= \sprintf('msgstr "%s"' . "\n", $this->escape($target));
        }
        return $output;
    }
    /**
     * {@inheritdoc}
     */
    protected function getExtension()
    {
        return 'po';
    }
    private function escape($str)
    {
        return \addcslashes($str, "\x00..\x1f\"\\");
    }
}
