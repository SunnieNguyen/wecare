<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WappoVendor\Symfony\Polyfill\Util;

if (\class_exists('WappoVendor\\PHPUnit_Runner_Version') && \version_compare(\WappoVendor\PHPUnit_Runner_Version::id(), '6.0.0', '<')) {
    \class_alias('WappoVendor\\Symfony\\Polyfill\\Util\\TestListenerForV5', 'WappoVendor\\Symfony\\Polyfill\\Util\\TestListener');
    // Using an early return instead of a else does not work when using the PHPUnit phar due to some weird PHP behavior (the class
    // gets defined without executing the code before it and so the definition is not properly conditional)
} elseif (\version_compare(\WappoVendor\PHPUnit\Runner\Version::id(), '7.0.0', '<')) {
    \class_alias('WappoVendor\\Symfony\\Polyfill\\Util\\TestListenerForV6', 'WappoVendor\\Symfony\\Polyfill\\Util\\TestListener');
} else {
    \class_alias('WappoVendor\\Symfony\\Polyfill\\Util\\TestListenerForV7', 'WappoVendor\\Symfony\\Polyfill\\Util\\TestListener');
}
if (false) {
    class TestListener
    {
    }
}
