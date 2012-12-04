<?php

require_once $_SERVER['SYMFONY_PATH'] . '/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespace('Symfony', $_SERVER['SYMFONY_PATH']);
$loader->registerNamespace('EC\\Bundle\\VagrantBundle', realpath(dirname(__FILE__) . '/../'));
$loader->register();