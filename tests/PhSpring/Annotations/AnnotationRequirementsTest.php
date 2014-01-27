<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Annotations;

use FilesystemIterator;
use PHPUnit_Framework_TestCase;
use PhSpring\Engine\AnnotationAbstract;
use ReflectionClass;

/**
 * Description of AnnotationRequirementsTest
 *
 * @author lobiferi
 */
class AnnotationRequirementsTest extends PHPUnit_Framework_TestCase {

    /**
     * @test
     */
    public function checkInstanceOf() {
        $iterator = new FilesystemIterator(preg_replace('/tests\\' . DIRECTORY_SEPARATOR . '/', '/lib/', __DIR__));
        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isFile()) {
                $className = __NAMESPACE__ . '\\' . substr($fileinfo->getBasename(), 0, -4);
                $refl = new ReflectionClass($className);
                if (preg_match('/@[Aa]nnotation/', $refl->getDocComment())) {
                    $this->assertTrue($refl->isSubclassOf(AnnotationAbstract::class), "The '{$className}' annotation class must extend the 'PhSpring\Engine\AnnotationAbstract'");
                }
            }
        }
    }

}
