<?php

if (is_file(__DIR__.'/bootstrap-local.php')) {
    include __DIR__.'/bootstrap-local.php';
}

require_once $_SERVER['SYMFONY'].'/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;
use Doctrine\Common\Annotations\AnnotationRegistry;

$loader = new UniversalClassLoader();
$loader->registerNamespace('Symfony', $_SERVER['SYMFONY']);
$loader->registerNamespace('Doctrine\\Common', $_SERVER['DOCTRINE_COMMON']);
$loader->registerNamespace('Doctrine\\DBAL', $_SERVER['DOCTRINE_DBAL']);
$loader->registerNamespace('Doctrine\\ORM', $_SERVER['DOCTRINE_ORM']);
$loader->registerNamespace('Crocos', __DIR__.'/../../..');
$loader->register();


AnnotationRegistry::registerLoader(function($class) use ($loader) {
    $loader->loadClass($class);
    return class_exists($class, false);
});

require_once $_SERVER['PHAKE'].'/tests/bootstrap.php';

if (is_readable($fbpath = $_SERVER['FACEBOOK'].'/facebook.php')) {
    require_once $fbpath;
}
