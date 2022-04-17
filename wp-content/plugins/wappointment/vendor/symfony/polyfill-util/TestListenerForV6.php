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
/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class TestListenerForV6 extends \WappoVendor\PHPUnit\Framework\TestSuite implements \WappoVendor\PHPUnit\Framework\TestListener
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
    public function startTestSuite(\WappoVendor\PHPUnit\Framework\TestSuite $suite)
    {
        $this->trait->startTestSuite($suite);
    }
    public function addError(\WappoVendor\PHPUnit\Framework\Test $test, \Exception $e, $time)
    {
        $this->trait->addError($test, $e, $time);
    }
    public function addWarning(\WappoVendor\PHPUnit\Framework\Test $test, \WappoVendor\PHPUnit\Framework\Warning $e, $time)
    {
    }
    public function addFailure(\WappoVendor\PHPUnit\Framework\Test $test, \WappoVendor\PHPUnit\Framework\AssertionFailedError $e, $time)
    {
        $this->trait->addError($test, $e, $time);
    }
    public function addIncompleteTest(\WappoVendor\PHPUnit\Framework\Test $test, \Exception $e, $time)
    {
    }
    public function addRiskyTest(\WappoVendor\PHPUnit\Framework\Test $test, \Exception $e, $time)
    {
    }
    public function addSkippedTest(\WappoVendor\PHPUnit\Framework\Test $test, \Exception $e, $time)
    {
    }
    public function endTestSuite(\WappoVendor\PHPUnit\Framework\TestSuite $suite)
    {
    }
    public function startTest(\WappoVendor\PHPUnit\Framework\Test $test)
    {
    }
    public function endTest(\WappoVendor\PHPUnit\Framework\Test $test, $time)
    {
    }
    public static function warning($message)
    {
        return parent::warning($message);
    }
    protected function setUp()
    {
        \WappoVendor\Symfony\Polyfill\Util\TestListenerTrait::$enabledPolyfills = $this->suite->getName();
    }
    protected function tearDown()
    {
        \WappoVendor\Symfony\Polyfill\Util\TestListenerTrait::$enabledPolyfills = false;
    }
}
