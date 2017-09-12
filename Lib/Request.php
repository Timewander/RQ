<?php

class Request {

    private static $path = [];
    private static $params = [];
    private static $payload = [];
    private static $method;
    private static $domain;
    private static $uri;
    private static $header = [];

    public static function setPath($path) {

        self::$path = $path;
    }

    public static function setParams($params) {

        self::$params = $params;
    }

    public static function setPayload($payload) {

        self::$payload = $payload;
    }

    public static function setServer($server) {

        self::$method = strtolower($server["REQUEST_METHOD"]);
        self::$domain = $server["SERVER_NAME"];
        self::$uri = $server["REQUEST_URI"];
    }

    public static function setHeader($header) {

        self::$header = $header;
    }

    public static function control() {

        $control = self::path(0);
        if (is_null($control)) {
            return null;
        }
        return strtoupper(substr($control, 0, 1)) . substr($control, 1) . "Controller";
    }

    public static function action() {

        return self::path(1);
    }

    public static function attribute() {

        return array_slice(self::$path, 2);
    }

    private static function path($index) {

        return isset(self::$path[$index]) ? self::$path[$index] : null;
    }

    public static function get($key, $default = null) {

        return isset(self::$params[$key]) ? self::$params[$key] : $default;
    }

    public static function post($key, $default = null) {

        return isset(self::$payload[$key]) ? self::$payload[$key] : $default;
    }

    public static function header($key, $default = null) {

        return isset(self::$header[$key]) ? self::$header[$key] : $default;
    }

    public static function params() {

        return self::$params;
    }

    public static function payload() {

        return self::$payload;
    }

    public static function method() {

        return self::$method;
    }

    public static function domain() {

        return self::$domain;
    }

    public static function uri() {

        return self::$uri;
    }

    public static function headers() {

        return self::$header;
    }

    public static function all() {

        return array_merge(self::$params, self::$payload);
    }
}