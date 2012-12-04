<?php

namespace EC\Bundle\VagrantBundle\Tests\Command;

use EC\Bundle\VagrantBundle\Command\VagrantGenerateCommand;
use PHPUnit_Framework_TestCase;

/**
 * @covers EC\Bundle\VagrantBundle\Command\VagrantGenerateCommand
 */
class VagrantGenerateCommandTest extends PHPUnit_Framework_TestCase
{
    public function testConfiguration()
    {
        $command = new VagrantGenerateCommand();

        $this->assertSame('generate:vagrant', $command->getName());
        $this->assertTrue(0 < strlen($command->getDescription()));
        $definition = $command->getDefinition();

        $this->assertSame(0, $definition->getArgumentCount());

        $options = $definition->getOptions();
        $this->assertSame(4, count($options));
    }

    public function testExecuteSuccess()
    {

    }
}