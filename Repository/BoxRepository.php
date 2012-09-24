<?php

namespace EC\Bundle\VagrantBundle\Repository;

use Symfony\Component\DomCrawler\Crawler;

class BoxRepository
{
    private $localBoxes;

    private $remoteBoxes;

    public function findAll()
    {
        $boxes = array_merge($this->findLocal(), $this->findRemote());

        ksort($boxes);

        return $boxes;
    }

    public function findLocal()
    {
        if (null === $this->localBoxes) {
            $names = explode("\n", trim(`vagrant box list`));

            $this->localBoxes = array_combine($names, $names);

            ksort($this->localBoxes);
        }

        return $this->localBoxes;
    }

    public function findRemote()
    {
        if (null === $this->remoteBoxes) {
            $this->remoteBoxes = array_merge(
                $this->getLiipBoxes(),
                $this->getVagrantBoxes()
            );

            ksort($this->remoteBoxes);
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

            $boxes[$name] = $baseHref.$link;
        }

        return $boxes;
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

        return array_combine($names, $urls);
    }
}
