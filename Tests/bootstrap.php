<?php

if (!isset($_SERVER['VENDOR_PATH'])) {
    throw new \InvalidArgumentException('No VENDOR_PATH provided.');
}

$vendorPath = $_SERVER['VENDOR_PATH'];

$symfonyPath = $vendorPath . '/symfony/symfony/src/';
$guzzlePath = $vendorPath . '/guzzle/guzzle/src/';

require_once $symfonyPath . 'Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespace('Symfony', $symfonyPath);
$loader->registerNamespace('Guzzle', $guzzlePath);
$loader->registerNamespace('EC\\Bundle\\VagrantBundle', realpath(dirname(__FILE__) . '/../../../../'));
$loader->register();