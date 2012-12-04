<?php

namespace EC\Bundle\VagrantBundle\Tests\Generator;

use EC\Bundle\VagrantBundle\Entity\Box;
use EC\Bundle\VagrantBundle\Generator\VagrantGenerator;
use PHPUnit_Framework_TestCase;

class VagrantGeneratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $directory;

    /**
     * @var VagrantGenerator
     */
    private $generator;

    /**
     * @var array
     */
    private $blocks = array(array('10.0.0.0', '10.255.255.255'),
                            array('172.16.0.0', '172.31.255.255'),
                            array('192.168.0.0', '192.168.255.255'));

    public function setUp()
    {
        $this->directory = tempnam('/tmp', 'vagrant-generator-test');
        unlink($this->directory);
        mkdir($this->directory);

        $this->generator = new VagrantGenerator(__DIR__ . '/../../Resources/skeleton/');
    }

    public function tearDown()
    {
        $dh = opendir($this->directory);
        while (false !== $file = readdir($dh)) {
            if ('.' !== substr($file, 0, 1)) {
                unlink($this->directory . '/' . $file);
            }
        }

        rmdir($this->directory);
    }

    public function testGenerateIp()
    {
        for ($i = 0; $i < 100; $i++) {
            $ip = ip2long(VagrantGenerator::generateIp());
            $found = false;
            foreach ($this->blocks as $block) {
                if ($ip > ip2long($block[0]) && $ip < ip2long($block[1])) {
                    $found = true;
                }
            }
            $this->assertTrue($found);
        }
    }

    public function testGenerateSucces()
    {
        $testHost = 'test-host-name';
        $testIp = '1.1.1.1';
        $testBox = new Box('test-box', 'http://example.com/test-box');

        $this->generator->generate($this->directory, array('host' => $testHost, 'ip' => $testIp, 'box' => $testBox));

        $vagrantFile = $this->directory . '/Vagrantfile';
        $this->assertFileExists($vagrantFile);
        $this->assertContains($testHost, file_get_contents($vagrantFile));
        $this->assertContains($testIp, file_get_contents($vagrantFile));
        $this->assertContains($testBox->getUrl(), file_get_contents($vagrantFile));
        $this->assertContains($testBox->getName(), file_get_contents($vagrantFile));
    }

    public function testGenerateFailure()
    {
        $this->setExpectedException('Exception');
        $this->generator->generate('\\///..//non-existing', array());
    }
}