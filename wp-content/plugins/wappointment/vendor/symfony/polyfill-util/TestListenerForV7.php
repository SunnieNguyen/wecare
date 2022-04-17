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

use WappoVendor\PHPUnit\Framework\AssertionFailedError;
use WappoVendor\PHPUnit\Framework\Test;
use WappoVendor\PHPUnit\Framework\TestListener as TestListenerInterface;
use WappoVendor\PHPUnit\Framework\TestSuite;
use WappoVendor\PHPUnit\Framework\Warning;
use WappoVendor\PHPUnit\Framework\WarningTestCase;
/**
 * @author Ion Bazan <ion.bazan@gmail.com>
 */
class TestListenerForV7 extends \WappoVendor\PHPUnit\Framework\TestSuite implements \WappoVendor\PHPUnit\Framework\TestListener
{
    private $suite;
    private $trait;
    public function __construct(\WappoVendor\PHPUnit\Framework\TestSuite $suite = null)
    {
        if ($suite) {
            $this->suite = $suite;
            $this->setName($suite->getName() . ' with polyfills enabled');
            $this->addTest($suite);
        }
        $this->trait = new \WappoVendor\Symfony\Polyfill\Util\TestListenerTrait();
    }
    public function startTestSuite(\WappoVendor\PHPUnit\Framework\TestSuite $suite) : void
    {
        $this->trait->startTestSuite($suite);
    }
    public function addError(\WappoVendor\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
        $this->trait->addError($test, $t, $time);
    }
    public function addWarning(\WappoVendor\PHPUnit\Framework\Test $test, \WappoVendor\PHPUnit\Framework\Warning $e, float $time) : void
    {
    }
    public function addFailure(\WappoVendor\PHPUnit\Framework\Test $test, \WappoVendor\PHPUnit\Framework\AssertionFailedError $e, float $time) : void
    {
        $this->trait->addError($test, $e, $time);
    }
    public function addIncompleteTest(\WappoVendor\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
    }
    public function addRiskyTest(\WappoVendor\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
    }
    public function addSkippedTest(\WappoVendor\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
    }
    public function endTestSuite(\WappoVendor\PHPUnit\Framework\TestSuite $suite) : void
    {
    }
    public function startTest(\WappoVendor\PHPUnit\Framework\Test $test) : void
    {
    }
    public function endTest(\WappoVendor\PHPUnit\Framework\Test $test, float $time) : void
    {
    }
    public static function warning($message) : \WappoVendor\PHPUnit\Framework\WarningTestCase
    {
        return new \WappoVendor\PHPUnit\Framework\WarningTestCase($message);
    }
    protected function setUp() : void
    {
        \WappoVendor\Symfony\Polyfill\Util\TestListenerTrait::$enabledPolyfills = $this->suite->getName();
    }
    protected function tearDown() : void
    {
        \WappoVendor\Symfony\Polyfill\Util\TestListenerTrait::$enabledPolyfills = false;
    }
}
