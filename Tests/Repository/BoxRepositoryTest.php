<?php

namespace EC\Bundle\VagrantBundle\Tests\Repository;

use EC\Bundle\VagrantBundle\Entity\Box;
use EC\Bundle\VagrantBundle\Repository\BoxRepository;
use PHPUnit_Framework_TestCase;

/**
 * @covers EC\Bundle\VagrantBundle\Repository\BoxRepository
 */
class BoxRepositoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var BoxRepository
     */
    private $repository;

    public function setUp()
    {
        $this->repository = new BoxRepository();
    }

    public function testGetVagrantBoxes()
    {
        $result = $this->repository->getVagrantBoxes();

        $this->assertInstanceOf('EC\Bundle\VagrantBundle\Collection\BoxCollection', $result);
        $this->assertTrue(5 < count($result));
    }

    public function testFindRemote()
    {
        $result = $this->repository->findRemote();
        $this->assertInstanceOf('EC\Bundle\VagrantBundle\Collection\BoxCollection', $result);

        $liipBoxFound = $vagrantBoxesBoxFound = false;
        foreach ($result as $box) {
            /** @var $box Box */
            if (false !== strpos($box->getUrl(), 'liip')) {
                $liipBoxFound = true;
            } else {
                $vagrantBoxesBoxFound = true;
            }
        }
        $this->assertTrue($vagrantBoxesBoxFound);
        $this->assertTrue($liipBoxFound);
    }

    public function testFindLocal()
    {
        $result = $this->repository->findLocal();
        $this->assertInstanceOf('EC\Bundle\VagrantBundle\Collection\BoxCollection', $result);

        $this->assertTrue(0 < count($result));
    }

    public function testFindAll()
    {
        $localBoxNames = array_map(function (Box $box) {
            return $box->getName();
        }, $this->repository->findLocal()->getChoices());
        $result = $this->repository->findAll();

        $liipBoxFound = $vagrantBoxesBoxFound = $localBoxFound = false;
        foreach ($result as $box) {
            /** @var $box Box */
            if (false !== strpos($box->getUrl(), 'liip')) {
                $liipBoxFound = true;
            } elseif (in_array($box->getName(), $localBoxNames)) {
                $localBoxFound = true;
            } else {
                $vagrantBoxesBoxFound = true;
            }
        }
        $this->assertTrue($vagrantBoxesBoxFound);
        $this->assertTrue($liipBoxFound);
        $this->assertTrue($localBoxFound);
    }

    public function testFind()
    {
        $boxes = $this->repository->findAll();

        foreach ($boxes as $box) {
            $this->assertSame($box, $this->repository->find($box->getName()));
        }
    }
}