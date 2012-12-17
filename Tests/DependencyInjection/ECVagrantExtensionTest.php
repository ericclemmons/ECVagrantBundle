<?php

namespace EC\Bundle\VagrantBundle\Tests\DependencyInjection;

use EC\Bundle\VagrantBundle\DependencyInjection\ECVagrantExtension;
use PHPUnit_Framework_TestCase;

/**
 * @author Paul Seiffert <paul.seiffert@gmail.com>
 */
class ECVagrantExtensionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers EC\Bundle\VagrantBundle\DependencyInjection\ECVagrantExtension::__construct
     */
    public function testInstantiation()
    {
        $extension = new ECVagrantExtension();
        $this->assertInstanceOf('EC\Bundle\VagrantBundle\DependencyInjection\ECVagrantExtension', $extension);
        $this->assertInstanceOf('Symfony\Component\HttpKernel\DependencyInjection\Extension', $extension);
    }

    /**
     * @covers EC\Bundle\VagrantBundle\DependencyInjection\ECVagrantExtension::load
     */
    public function testLoadAddsServicesYml()
    {
        $extension = new ECVagrantExtension();
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder', array('addResource'));

        $container->expects($this->once())
            ->method('addResource')
            ->with($this->isInstanceOf('Symfony\Component\Config\Resource\FileResource'));

        $extension->load(array(), $container);
    }
}