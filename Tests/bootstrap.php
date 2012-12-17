<?php

if (!isset($_SERVER['VENDOR_PATH'])) {
    throw new \InvalidArgumentException('No VENDOR_PATH provided.');
}

$vendorPath = $_SERVER['VENDOR_PATH'];

$symfonyPath = $vendorPath . '/symfony/symfony/src/';
$guzzlePath = $vendorPath . '/guzzle/guzzle/src/';
$sensioGeneratorPath = $vendorPath . '/sensio/generator-bundle/';
$twigPath = $vendorPath . '/twig/twig/lib';

require_once $symfonyPath . 'Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespace('Symfony', $symfonyPath);
$loader->registerNamespace('Guzzle', $guzzlePath);
$loader->registerNamespace('Sensio\\Bundle\\GeneratorBundle', $sensioGeneratorPath);
$loader->registerPrefix('Twig_', $twigPath);
$loader->registerNamespace('EC\\Bundle\\VagrantBundle', realpath(dirname(__FILE__) . '/../../../../'));
$loader->register();