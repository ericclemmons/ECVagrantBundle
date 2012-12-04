<?php

namespace EC\Bundle\VagrantBundle\Tests\Collection;

use PHPUnit_Framework_TestCase;
use EC\Bundle\VagrantBundle\Collection\BoxCollection;
use EC\Bundle\VagrantBundle\Entity\Box;

/**
 * @covers EC\Bundle\VagrantBundle\Collection\BoxCollection
 */
class BoxCollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var BoxCollection
     */
    private $collection;

    /**
     *
     */
    public function setUp()
    {
        $this->collection = new BoxCollection();
    }

    public function testInterfaces()
    {
        $this->assertInstanceOf('Countable', $this->collection);
        $this->assertInstanceOf('IteratorAggregate', $this->collection);
    }

    public function testConstructorMerges()
    {
        $boxes = $this->getExampleBoxes();

        $this->collection = new BoxCollection($boxes);
        $this->assertSame(count($boxes), count($this->collection));

        $countBeforeAdditionalMerge = count($this->collection);
        $this->collection->merge($boxes);

        $this->assertSame($countBeforeAdditionalMerge, count($this->collection));
    }

    public function testCountZero()
    {
        $this->assertEquals(0, count($this->collection));
    }

    public function testCount()
    {
        $boxes = $this->getExampleBoxes();

        $this->collection->merge($boxes);

        $this->assertEquals(count($boxes), count($this->collection));
        $this->assertEquals(count($this->collection), $this->collection->count());

        $this->collection->set('testx', new Box('testx'));
        $this->assertEquals(count($boxes) + 1, count($this->collection));
    }

    public function testGetNotFound()
    {
        $this->assertNull($this->collection->get('xyz'));
    }

    public function testGetSuccess()
    {
        $boxName = 'box';
        $box = new Box($boxName);

        $this->collection->set($boxName, $box);

        $this->assertSame($box, $this->collection->get($boxName));
    }

    public function testGetChoicesEmpty()
    {
        $this->assertEquals(array(), $this->collection->getChoices());
    }

    public function testGetChoices()
    {
        $boxes = $this->getExampleBoxes();
        ksort($boxes);

        $this->collection->merge($boxes);

        $choices = $this->collection->getChoices();
        for ($i = 0; $i < count($boxes); $i++) {
            $this->assertSame($boxes[$i], $choices[$i + 1]);
        }
    }

    public function testGetChoiceNotFound()
    {
        $this->assertNull($this->collection->getChoice(5));
    }

    public function testGetChoiceSuccess()
    {
        $boxes = $this->getExampleBoxes();
        ksort($boxes);

        $this->collection->merge($boxes);

        for ($i = 0; $i < count($boxes); $i++) {
            $this->assertSame($boxes[$i], $this->collection->getChoice($i + 1));
        }
    }

    public function testGetIteratorEmpty()
    {
        $iterator = $this->collection->getIterator();

        $this->assertInstanceOf('ArrayIterator', $iterator);
        $this->assertEquals(0, $iterator->count());
    }

    public function testGetIteratorNotEmpty()
    {
        $boxes = $this->getExampleBoxes();

        $this->collection->merge($boxes);

        $iterator = $this->collection->getIterator();
        $this->assertSame(count($boxes), $iterator->count());
    }

    public function testMerge()
    {
        $boxes = $this->getExampleBoxes();

        $result = $this->collection->merge($boxes);
        $this->assertSame($this->collection, $result);

        foreach ($boxes as $box) {
            $this->assertSame($box, $this->collection->get($box->getName()));
        }
        $this->assertSame(count($boxes), count($this->collection));
    }

    public function testSet()
    {
        $box = new Box('test');

        $result = $this->collection->set($box->getName(), $box);
        $this->assertSame($this->collection, $result);

        $this->assertSame($box, $this->collection->get($box->getName()));
    }

    /**
     * @return array|Box[]
     */
    private function getExampleBoxes()
    {
        return array(new Box('test1'),
                     new Box('test2'),
                     new Box('test3'));
    }
}