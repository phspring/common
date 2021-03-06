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
if (!defined('PHSPRING_TEST_PATH')) {
    define('PHSPRING_TEST_PATH', __DIR__ . '/PhSpring');
}
if (!defined('PHSPRING_FIXTURES_PATH')) {
    define('PHSPRING_FIXTURES_PATH', PHSPRING_TEST_PATH . '/TestFixtures');
}

require_once __DIR__ . "/../vendor/autoload.php";
//require_once __DIR__ . "/PHPUnit/Util/Test.php";

\Doctrine\Common\Annotations\AnnotationRegistry::registerAutoloadNamespace(
        'Doctrine\Tests\Common\Annotations\Fixtures', __DIR__ . '/../../'
);

\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');
/*
\Doctrine\Common\Annotations\AnnotationRegistry::registerAutoloadNamespace(
        'PhSpring\Annotations', __DIR__ . '/../lib/'
);

$dir = dirname((new \ReflectionClass('Symfony\Component\Validator\Constraints\Valid'))->getFileName());

\Doctrine\Common\Annotations\AnnotationRegistry::registerAutoloadNamespace(
        "Symfony\Component\Validator\Constraints", 
        str_replace("Symfony/Component/Validator/Constraints", '', $dir)
);
*/