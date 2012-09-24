<?php

namespace EC\Bundle\VagrantBundle\Entity;

class Box
{
    private $name;

    private $url;

    static public function fromUrl($url)
    {
        return new static(pathinfo($url, PATHINFO_FILENAME), $url);
    }

    public function __construct($name, $url = null)
    {
        $this->name = $name;
        $this->url  = $url;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function isLocal()
    {
        return !isset($this->url);
    }

    public function isRemote()
    {
        return isset($this->url);
    }

    public function __toString()
    {
        return $this->name;
    }
}
