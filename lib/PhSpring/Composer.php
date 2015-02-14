<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring;

/**
 * Description of Composer
 *
 * @author lobiferi
 */
class Composer {
    public static function addAnnotationNamespaces( \Composer\Script\Event $event){
        $annotationNamespaces = array();
        $annotationNamespaces['PhSpring\Annotations'] = dirname(dirname(dirname((new \ReflectionClass('PhSpring\Annotations\Config'))->getFileName())));
        $annotationNamespaces['Symfony\Component\Validator'] = dirname(dirname(dirname(dirname((new \ReflectionClass('Symfony\Component\Validator\Validator\RecursiveValidator'))->getFileName()))));
        $binDir = dirname(dirname($annotationNamespaces['Symfony\Component\Validator'])).'/bin/phspring';
        if(!is_dir($binDir)){
            mkdir($binDir);
        }
        $fh = fopen($binDir.'/annotation.json','w+');
        fwrite($fh, json_encode($annotationNamespaces));
        fclose($fh);
    }
}
