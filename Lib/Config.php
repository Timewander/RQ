<?php

class Config {

    private static $env = null;

    private static function init() {

        include ENV;
        if (isset($env) && !empty($env)) {
            self::$env = $env;
        }

        return self::$env;
    }

    public static function get($key, $default = null) {

        $env = is_null(self::$env) ? self::init() : self::$env;
        return isset($env[$key]) ? $env[$key] : $default;
    }
}