<?php

class Swse {

    public static function webservice_quality($uri) {

        $host = "https://swset-cn-cartier-quality.intranet.rccad.net:8443/webservices";
        $url = $host . $uri;
        if (!is_null(Request::get("wsdl"))) {
           return self::wsdl($url);
        }
        SwseHandler::$url = $url;
        SwseHandler::$usr = "swseCartierQual";
        SwseHandler::$psw = "swseq@car2015";

        $server = new SoapServer("http://" . Request::domain() . Request::uri() . "?wsdl", []);
        $server->setClass("SwseHandler");
        $server->handle();
        return null;
    }

    public static function rest_quality() {

        $host = "https://swset-cn-cartier-quality.intranet.rccad.net:8443";
        $url = $host . Request::uri();
        $response = Proxy::dealProxy($url);
        echo $response;
        return null;
    }

    private static function wsdl($url) {

        if (config("WSDL_CACHE", true)) {
            $response = self::load_from_cache($url);
        } else {
            $response = self::load_by_request($url);
        }
        header("Content-Type: text/xml;charset=UTF-8");
        echo $response;
        return null;
    }

    private static function load_by_request($url) {

        $payload = [
            "url" => $url,
            "body" => Request::payload(),
            "method" => Request::method(),
            "header" => Http::setHeader(Request::headers()),
        ];
        return Proxy::postRequest($payload);
    }

    private static function load_from_cache($url) {

        $redis = new RedisBase();
        $response = $redis->RedisString($url)->get();
        if ($response === false) {
            $response = self::load_by_request($url);
            $redis->RedisString($url)->set($response);
            $ttl = max(intval(config("WSDL_CACHE_TTL", 3600)), 60);
            $redis->expire($url, $ttl);
        }
        return $response;
    }
}