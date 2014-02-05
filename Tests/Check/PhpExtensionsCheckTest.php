<?php

namespace Liip\Monitor\Tests\Check;

use Liip\Monitor\Check\PhpExtensionsCheck;
use Liip\Monitor\Result\CheckResult;

class PhpExtensionsCheckTest extends \PHPUnit_Framework_TestCase
{
    public function testCheckLoadedExtensions()
    {
        $check = new PhpExtensionsCheck(array('SPL'));
        $result = $check->check();

        $this->assertInstanceOf('Liip\Monitor\Result\CheckResult', $result);
        $this->assertEquals(CheckResult::OK, $result->getStatus());
    }

    public function testCheckUnknownExtensions()
    {
        $check = new PhpExtensionsCheck(array('SPL', 'unknown1', 'unknown2'));
        $result = $check->check();

        $this->assertInstanceOf('Liip\Monitor\Result\CheckResult', $result);
        $this->assertEquals(CheckResult::CRITICAL, $result->getStatus());
        $this->assertNotContains('SPL', $result->getMessage());
        $this->assertContains('unknown1', $result->getMessage());
        $this->assertContains('unknown2', $result->getMessage());
    }
}
