<?php

class Config {

    private static $env = null;

    private static function init() {

        include ENV;
        include RESOURCE;
        if (isset($env) && !empty($env) && isset($resource) && !empty($resource)) {
            self::$env = array_merge($resource, $env);
        } else {
            self::$env = [];
        }

        return self::$env;
    }

    public static function get($key, $default = null) {

        $env = is_null(self::$env) ? self::init() : self::$env;
        return isset($env[$key]) ? $env[$key] : $default;
    }
}