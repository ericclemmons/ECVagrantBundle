<?php

namespace EC\Bundle\VagrantBundle\Repository;

use EC\Bundle\VagrantBundle\Entity\Box;
use EC\Bundle\VagrantBundle\Collection\BoxCollection;
use Symfony\Component\DomCrawler\Crawler;

class BoxRepository
{
    private $localBoxes;

    private $remoteBoxes;

    public function find($name)
    {
        return $this->findAll()->get($name);
    }

    public function findAll()
    {
        return $this->findLocal()->merge($this->findRemote());
    }

    public function findLocal()
    {
        if (null === $this->localBoxes) {
            $names = explode("\n", trim(`vagrant box list`));

            $boxes = array_map(function($name) {
                return new Box($name);
            }, $names);

            $this->localBoxes = new BoxCollection($boxes);
        }

        return $this->localBoxes;
    }

    public function findRemote()
    {
        if (null === $this->remoteBoxes) {
            $this->remoteBoxes = new BoxCollection();

            $this->remoteBoxes->merge($this->getLiipBoxes());
            $this->remoteBoxes->merge($this->getVagrantBoxes());
        }

        return $this->remoteBoxes;
    }

    private function getLiipBoxes()
    {
        $baseHref   = 'http://vagrantbox.liip.ch/';
        $crawler    = new Crawler(file_get_contents($baseHref));
        $links      = $crawler->filter('td a')->reduce(function($node) {
            return 'box' === pathinfo($node->getAttribute('href'), PATHINFO_EXTENSION);
        })->extract('href');

        $boxes = array();

        foreach ($links as $link) {
            $parts  = explode('liip-', basename($link, '.box'));
            $name   = end($parts);

            $boxes[] = new Box($name, $baseHref.$link);
        }

        return new BoxCollection($boxes);
    }

    public function getVagrantBoxes()
    {
        $crawler    = new Crawler(file_get_contents('http://www.vagrantbox.es/'));
        $rows       = $crawler->filter('table tr');

        $names = $rows->each(function($node) {
            return $node->getElementsByTagName('th')->item(0)->textContent;
        });

        $urls = $rows->each(function($node) {
            return $node->getElementsByTagName('td')->item(0)->textContent;
        });

        $boxes = array_combine($names, $urls);

        foreach ($boxes as $name => $url) {
            $boxes[$name] = new Box($name, $url);
        }

        return new BoxCollection($boxes);
    }
}
