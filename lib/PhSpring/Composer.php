<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring;
use Symfony\Component\Validator\Validator\RecursiveValidator;
/**
 * Description of Composer
 *
 * @author lobiferi
 */
class Composer {
    public static function addAnnotationNamespaces( \Composer\Script\Event $event){
        $annotationNamespaces = array();
        $annotationNamespaces['PhSpring\Annotations'] = dirname(dirname(dirname((new \ReflectionClass('PhSpring\Annotations\Config'))->getFileName())));
        $annotationNamespaces['Symfony\Component\Validator'] = dirname(dirname(dirname(dirname(dirname((new \ReflectionClass('Symfony\Component\Validator\Validator\RecursiveValidator'))->getFileName())))));
        $binDir = getcwd().'/bin';
        if(!is_dir($binDir)){
            mkdir($binDir, 0755, true);
        }
        $fh = fopen($binDir.'/phspring-annotation.js','w+');
        fwrite($fh, json_encode($annotationNamespaces));
        fclose($fh);
    }
}
