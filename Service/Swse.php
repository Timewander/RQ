<?php

class Swse {

    public static function webservice_quality($uri) {

        $host = config("swse_soap_quality", "");
        $url = $host . $uri;
        if (!is_null(Request::get("wsdl"))) {
           return self::wsdl_quality($url);
        }
        SwseHandler::$url = $url;
        SwseHandler::$usr = !is_null(Request::auth_usr()) ? Request::auth_usr() : config("swse_username_quality", "");
        SwseHandler::$psw = !is_null(Request::auth_pwd()) ? Request::auth_pwd() : config("swse_password_quality", "");

        $server = new SoapServer(Request::url() . "?wsdl", []);
        $server->setClass("SwseHandler");
        $server->handle();
        return response();
    }

    public static function rest_quality() {

        $host = config("swse_rest_quality", "");
        $url = $host . Request::uri();
        $response = Proxy::dealProxy($url, "quality");
        return response($response);
    }

    private static function wsdl_quality($url) {

        if (config("WSDL_CACHE", true)) {
            $response = self::load_quality_from_cache($url);
        } else {
            $response = self::load_quality_by_request($url);
        }
        return response($response, 200, ["Content-Type: text/xml;charset=UTF-8"]);
    }

    private static function load_quality_by_request($url) {

        $payload = [
            "url" => $url,
            "body" => Request::payload(),
            "method" => Request::method(),
            "header" => Http::setHeader(Request::headers()),
        ];
        $response = Proxy::postRequest($payload);
        return str_replace(config("swse_wsdl_location_quality", ""), Request::proxy_domain(), $response);
    }

    private static function load_quality_from_cache($url) {

        $redis = new RedisBase();
        $response = $redis->RedisString($url)->get();
        if ($response === false) {
            $response = self::load_quality_by_request($url);
            $redis->RedisString($url)->set($response);
            $ttl = max(intval(config("WSDL_CACHE_TTL", 3600)), 60);
            $redis->expire($url, $ttl);
        }
        return $response;
    }
}