<?php

namespace EC\Bundle\VagrantBundle\Repository;

use EC\Bundle\VagrantBundle\Entity\Box;
use EC\Bundle\VagrantBundle\Collection\BoxCollection;
use EC\Bundle\VagrantBundle\Repository\Exception\HttpException;
use Guzzle\Service\Client;
use Guzzle\Http\Exception\HttpException as GuzzleHttpException;
use Symfony\Component\DomCrawler\Crawler;

/**
 * @author Eric Clemmons <eric@smarterspam.com>
 * @author Paul Seiffert <paul.seiffert@gmail.com>
 */
class BoxRepository
{
    /**
     * @var BoxCollection
     */
    private $localBoxes;

    /**
     * @var BoxCollection
     */
    private $remoteBoxes;

    /**
     * @param string $name
     * @return Box
     */
    public function find($name)
    {
        return $this->findAll()->get($name);
    }

    /**
     * @return BoxCollection
     */
    public function findAll()
    {
        $local = clone $this->findLocal();

        return $local->merge($this->findRemote());
    }

    /**
     * @return BoxCollection
     */
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

    /**
     * @return BoxCollection
     */
    public function findRemote()
    {
        if (null === $this->remoteBoxes) {
            $this->remoteBoxes = new BoxCollection();

            $this->remoteBoxes->merge($this->getLiipBoxes());
            $this->remoteBoxes->merge($this->getVagrantBoxes());
        }

        return $this->remoteBoxes;
    }

    /**
     * @return BoxCollection
     */
    private function getLiipBoxes()
    {
        $baseUri = 'http://vagrantbox.liip.ch/';
        $crawler = new Crawler($this->fetchWebpage($baseUri));

        $boxUris = $crawler->filter('td a')->reduce(function($node) {
            return 'box' === pathinfo($node->getAttribute('href'), PATHINFO_EXTENSION);
        })->extract('href');

        $boxes = array();
        foreach ($boxUris as $boxUri) {
            $parts  = explode('liip-', basename($boxUri, '.box'));
            $name   = end($parts);

            $boxes[] = new Box($name, $baseUri . $boxUri);
        }

        return new BoxCollection($boxes);
    }

    /**
     * @return BoxCollection
     */
    public function getVagrantBoxes()
    {
        $crawler    = new Crawler($this->fetchWebpage('http://www.vagrantbox.es/'));
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

    /**
     * @param string $uri
     * @return string
     * @throws HttpException
     */
    private function fetchWebpage($uri)
    {
        $client = new Client();

        $request = $client->get($uri);

        try {
            $response = $request->send();
        } catch (GuzzleHttpException $e) {
            /** @var $e \Exception */
            throw new HttpException('Could not fetch URI "' . $uri . '".', $e->getCode());
        }

        return $response->getBody(true);
    }
}
