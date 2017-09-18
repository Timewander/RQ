<?php

class Response {

    private static $content = "";
    private static $headers = [];

    public static function build($content = "", $http_code = 200, $headers = []) {

        self::setContent($content);
        http_response_code($http_code);
        self::appendHeaders($headers);
        self::send();
    }

    public static function send() {

        $headers = Http::setHeader(self::$headers);
        foreach ($headers as $header) {
            header($header);
        }
        print self::$content;
        die;
    }

    public static function setContent($content) {

        if (!is_scalar($content)) {
            $content = json_encode($content);
            self::appendHeaders(["Content-Type" => "application/json"]);
        }
        self::$content = $content;
    }

    public static function setHeaders($headers) {

        self::$headers = Http::getHeader($headers);
    }

    public static function appendHeaders($headers) {

        self::$headers = array_merge(self::$headers, Http::getHeader($headers));
    }
}