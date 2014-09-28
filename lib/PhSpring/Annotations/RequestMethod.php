<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Annotations;

/**
 * Description of RequestMethod
 *
 * @author lobiferi
 */
class RequestMethod {

    const GET = 1;

    /** @see http://students.mimuw.edu.pl/~ai292615/php_head_trick.pdf */
    const HEAD = 2;
    const POST = 4;
    const PUT = 8;
    const PATCH = 16;
    const DELETE = 32;
    const OPTIONS = 64;
    const XMLHTTPREQUEST = 128;
    const ALL = 255;

    public static function valid($value) {
        return !!(self::ALL & $value);
    }

}
