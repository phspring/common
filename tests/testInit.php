<?php

error_reporting(E_ALL | E_STRICT);

// register silently failing autoloader
spl_autoload_register(function($class) {
    if (0 === strpos($class, 'PhSpring\\')) {
        $path = __DIR__ . '/../lib/' . strtr($class, '\\', '/') . '.php';
        if (is_file($path) && is_readable($path)) {
            require_once $path;

            return true;
        }
        return FALSE;
    }
});

spl_autoload_register(function($class) {
    if (0 === strpos($class, 'PhSpring\\TestFixtures\\')) {
        $path = __DIR__ . '/' . strtr($class, '\\', '/') . '.php';
        if (is_file($path) && is_readable($path)) {
            require_once $path;

            return true;
        }
        return FALSE;
    }
});

define('PHSPRING_TEST_PATH', __DIR__.'/PhSpring');
define('PHSPRING_FIXTURES_PATH', PHSPRING_TEST_PATH.'/TestFixtures');


require_once __DIR__ . "/../vendor/autoload.php";

\Doctrine\Common\Annotations\AnnotationRegistry::registerAutoloadNamespace(
        'Doctrine\Tests\Common\Annotations\Fixtures', __DIR__ . '/../../'
);
\Doctrine\Common\Annotations\AnnotationRegistry::registerAutoloadNamespace(
        'PhSpring\Annotations', __DIR__ . '/../lib/'
);
