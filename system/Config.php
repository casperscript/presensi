<?php

/**
 * frameduzPHP v4
 *
 * @Author  	: M. Hanif Afiatna <hanif.softdev@gmail.com>
 * @Since   	: version 4.1.0
 * @Date	: 10 April 2017
 * @package 	: core system
 * @Description : 
 */

namespace system;

class Config {

    private static $config = array();

    public static function Load($name) {
        if (!isset(self::$config[$name])) {
            $dataArr = require CONFIG . $name . '.php';
            self::$config[$name] = $dataArr;
            return $dataArr;
        } else {
            return self::$config[$name];
        }
    }

}