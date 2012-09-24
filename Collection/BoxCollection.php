<?php

namespace EC\Bundle\VagrantBundle\Collection;

use ArrayIterator, Countable, IteratorAggregate;
use EC\Bundle\VagrantBundle\Entity\Box;

class BoxCollection implements Countable, IteratorAggregate
{
    private $boxes = array();

    public function __construct(array $boxes = array())
    {
        $this->merge($boxes);
    }

    public function count()
    {
        return count($this->boxes);
    }

    public function get($key)
    {
        return isset($this->boxes[$key]) ? $this->boxes[$key] : null;
    }

    public function getChoice($choice)
    {
        $choices = $this->getChoices();

        return isset($choices[$choice]) ? $choices[$choice] : null;
    }

    public function getChoices()
    {
        $choices    = array();
        $choice     = 1;

        foreach ($this as $box) {
            $choices[$choice++] = $box;
        }

        return $choices;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->boxes);
    }

    public function merge($boxes)
    {
        foreach ($boxes as $box) {
            $this->set($box->getName(), $box);
        }

        ksort($this->boxes);

        return $this;
    }

    public function set($name, Box $box)
    {
        $this->boxes[$name] = $box;

        return $this;
    }
}
