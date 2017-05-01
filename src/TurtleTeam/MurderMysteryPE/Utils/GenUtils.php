<?php

namespace TurtleTeam\MurderMysteryPE\Utils;
final class GenUtils{
    /**
     * @param array    $vars
     * @param callable $closure
     * @param integer  $iterator
     */
    public static function loop(Array $vars, $closure, &$iterator = 0){
            foreach($vars as $key => $var){
                    $closure($key, $var);
                    ++$iterator;
            }
    }

    /**
     * @param string $string
     * @param array  $params
     *
     * @return string
     */
    public static function formatter($string, ...$params){

            if(is_array($params[0])) goto A;

            if(count($params) == 1){
                    $string = str_replace("%1", $params, $string);
            }else{
                    A:
                    $i = 0;
                    if(is_array($params[0])){
                            self::loop($params[0], function ($key, $val) use (&$i, &$string) {
                                    $string = str_replace(("%" . ($key + 1)), $val, $string);
                            }, $i);
                    }else{
                            self::loop($params, function ($key, $val) use (&$i, &$string) {
                                    $string = str_replace(("%" . ($key + 1)), $val, $string);
                            }, $i);
                    }
            }

            return $string;
    }

    /**
     * @param string $message
     * @param string $replacerSymbol
     *
     * @return string
     */
    public static function textColoration($message, $replacerSymbol = "&"){
            return str_replace($replacerSymbol, "\xca\xa7", $message);
    }
}