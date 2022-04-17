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

/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class TestListenerForV5 extends \WappoVendor\PHPUnit_Framework_TestSuite implements \WappoVendor\PHPUnit_Framework_TestListener
{
    private $suite;
    private $trait;
    public function __construct(\WappoVendor\PHPUnit_Framework_TestSuite $suite = null)
    {
        if ($suite) {
            $this->suite = $suite;
            $this->setName($suite->getName() . ' with polyfills enabled');
            $this->addTest($suite);
        }
        $this->trait = new \WappoVendor\Symfony\Polyfill\Util\TestListenerTrait();
    }
    public function startTestSuite(\WappoVendor\PHPUnit_Framework_TestSuite $suite)
    {
        $this->trait->startTestSuite($suite);
    }
    public function addError(\WappoVendor\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        $this->trait->addError($test, $e, $time);
    }
    public function addWarning(\WappoVendor\PHPUnit_Framework_Test $test, \WappoVendor\PHPUnit_Framework_Warning $e, $time)
    {
    }
    public function addFailure(\WappoVendor\PHPUnit_Framework_Test $test, \WappoVendor\PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        $this->trait->addError($test, $e, $time);
    }
    public function addIncompleteTest(\WappoVendor\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
    }
    public function addRiskyTest(\WappoVendor\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
    }
    public function addSkippedTest(\WappoVendor\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
    }
    public function endTestSuite(\WappoVendor\PHPUnit_Framework_TestSuite $suite)
    {
    }
    public function startTest(\WappoVendor\PHPUnit_Framework_Test $test)
    {
    }
    public function endTest(\WappoVendor\PHPUnit_Framework_Test $test, $time)
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
