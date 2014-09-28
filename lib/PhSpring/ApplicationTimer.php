<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring;

/**
 * Description of ApplicationTimer
 *
 * @author lobiferi
 */
class ApplicationTimer {

    private static $draw = false;
    private static $items = array();
    private static $stack = array();
    private static $tree = array();
    private static $lastTime = null;
    private static $depths = array();

    public function __construct() {
        self::start();
        register_shutdown_function(array($this, 'shutdown'));
    }

    private static function add(array $stack) {
        $stack['start'] = microtime(true);
        $id = (int) $stack['start'] . (int) (($stack['start'] - (int) $stack['start']) * 100000000);

        self::$stack[$id] = $stack;
        self::$depths[] = $id;
        if (sizeof(self::$depths) >= 2) {
            $depth = self::$depths[sizeof(self::$depths) - 2];
            if (!isset(self::$tree[$depth])) {
                self::$tree[$depth] = array();
            }
            self::$tree[$depth][] = $id;
        }
    }

    private static function remove($stack) {
        $stack['stop'] = microtime(true);
        $id = array_pop(self::$depths);
        self::$stack[$id]['to'] = $stack['from'];
        self::$stack[$id]['stop'] = $stack['stop'];
        self::$stack[$id]['delta'] = $stack['stop'] - self::$stack[$id]['start'];
    }

    public static function start($message = null) {
        $trace = self::getTrace();
        if ($message) {
            $trace['point'] .= '&nbsp;&nbsp;(' . $message . ')';
        }
        self::add($trace);
    }

    public static function stop() {
        self::remove(self::getTrace());
    }

    private static function getTrace() {
        $trace = debug_backtrace(0, 3);
        //$trace = array(1=>array('line'=>''),2=>array('class'=>'', 'function'=>''));
        return array('point' => sprintf('%s::%s', $trace[2]['class'], $trace[2]['function']), 'from' => $trace[1]['line']);
    }

    public static function drawDeltaTime($timeFrom = APPLICATION_START_TIME, $message = '') {
        $t = microtime(true);
        $time = round((($t - $timeFrom) * 1000), 2) . " msec";
        $message = $time . ($message ? ' : ' . str_replace('\\', '\\\\', $message) : '');
        self::$items[] = $message;
        if (self::$draw) {
            if (!filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH')):
                foreach (self::$items as $item) {
                    ?><script type="text/javascript">var div = document.createElement('div');
                                            div.innerHTML = '<?php echo $item; ?>';
                                            div.style.border = '1px solid black';
                                            div.style.border = '1px solid black';
                                            div.style.backgroundColor = 'grey';
                                            div.style.color = 'white';
                                            document.body.appendChild(div);</script><?php
                }
            else:
                foreach (self::$items as $key => $item) {
                    header("ApplicationTimer[$key]: " . $item);
                }
            endif;
        }
    }

    function shutdown() {
        //self::$draw = true;
        self::stop();
        reset(self::$stack);
        echo self::draw(key(self::$stack));
    }

    private static function draw($id) {
        return null;
        static $depth = 0;
        if (!filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH')) {
            $depth++;
            $ret = '<div style="padding-left:20px;border: 1px solid #' . ($depth % 3 == 1 ? 'f00' : ($depth % 3 == 2 ? '0f0' : '00f')) . '">' . sprintf('%f msec&nbsp;&nbsp;&nbsp;%s [%d - %d]', round((isset(self::$stack[$id]['delta'])?self::$stack[$id]['delta']:1) * 1000, 2), self::$stack[$id]['point'], self::$stack[$id]['from'], isset(self::$stack[$id]['to'])?self::$stack[$id]['to']:'');
            if (isset(self::$tree[$id])) {
                foreach (self::$tree[$id] as $item) {
                    $ret .= self::draw($item);
                }
            }
            $ret .= '</div>';
            $depth--;
            return $ret;
        }
    }

}
