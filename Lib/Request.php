<?php

class Request {

    private static $path = [];
    private static $params = [];
    private static $payload = [];
    private static $cookie = [];
    private static $method;
    private static $domain;
    private static $uri;
    private static $url;
    private static $auth_usr;
    private static $auth_pwd;
    private static $proxy_domain;
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

    public static function setCookie($cookie) {

        self::$cookie = $cookie;
    }

    public static function setServer($server) {

        self::$method = strtolower($server["REQUEST_METHOD"]);
        self::$domain = $server["SERVER_NAME"];
        self::$uri = $server["REQUEST_URI"];
        self::$url = "http://" . self::$domain . self::$uri;
        self::$auth_usr = isset($server["PHP_AUTH_USER"]) ? $server["PHP_AUTH_USER"] : null;
        self::$auth_pwd = isset($server["PHP_AUTH_PW"]) ? $server["PHP_AUTH_PW"] : null;
    }

    public static function setProxyDomain($domain) {

        self::$proxy_domain = $domain;
    }

    public static function setHeader($header) {

        self::$header = $header;
    }

    public static function control() {

        $control = self::path(0);
        return is_null($control) ? null : strtoupper(substr($control, 0, 1)) . substr($control, 1) . "Controller";
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

    public static function cookie($key, $default = null) {

        return isset(self::$cookie[$key]) ? self::$cookie[$key] : $default;
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

    public static function cookies() {

        return self::$cookie;
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

    public static function url() {

        return self::$url;
    }

    public static function auth_usr() {

        return self::$auth_usr;
    }

    public static function auth_pwd() {

        return self::$auth_pwd;
    }

    public static function proxy_domain() {

        return self::$proxy_domain;
    }

    public static function headers() {

        return self::$header;
    }

    public static function all() {

        return array_merge(self::$params, self::$payload);
    }
}