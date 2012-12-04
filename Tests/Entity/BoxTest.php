<?php

namespace EC\Bundle\VagrantBundle\Tests\Entity;

use EC\Bundle\VagrantBundle\Entity\Box;
use PHPUnit_Framework_TestCase;

/**
 * @covers EC\Bundle\VagrantBundle\Entity\Box
 */
class BoxTest extends PHPUnit_Framework_TestCase
{
    public function testCreationFromUrl()
    {
        $testBaseUrl = 'http://example.com/';
        $testName = 'test-box';

        $box = Box::fromUrl($testBaseUrl . $testName);

        $this->assertSame($testName, $box->getName());
        $this->assertSame($testBaseUrl . $testName, $box->getUrl());
    }

    public function testConstructionWithoutUrl()
    {
        $testName = 'test-box';

        $box = new Box($testName);
        $this->assertSame($testName, $box->getName());
    }

    public function testConstructionWithUrl()
    {
        $testName = 'test-box';
        $testUrl = 'http://example.com/test-box';

        $box = new Box($testName, $testUrl);
        $this->assertSame($testName, $box->getName());
        $this->assertSame($testUrl, $box->getUrl());
    }

    public function testIsLocalTrue()
    {
        $box = new Box('test');
        $this->assertTrue($box->isLocal());
    }

    public function testIsLocalFalse()
    {
        $box = new Box('test', 'http://example.com/box-url');
        $this->assertFalse($box->isLocal());
    }

    public function testIsRemoteFalse()
    {
        $box = new Box('test');
        $this->assertFalse($box->isRemote());
    }

    public function testIsRemoteTrue()
    {
        $box = new Box('test', 'http://example.com/box-url');
        $this->assertTrue($box->isRemote());
    }

    public function testToString()
    {
        $testName = 'test-box';
        $box = new Box($testName);

        $this->assertEquals($testName, (string)$box);
    }
}