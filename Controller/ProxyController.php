<?php

class ProxyController {

	public static function handler() {
		$action = Request::action();
        $method = Request::method();
		switch ("$action:$method") {
            case "request:get" :
            case "request:post" :
                return self::request();
            case "response:post" :
                return self::response();
            case "power:get" :
                return self::power();
            case "getOauth:get" :
                return self::getOauth();
            case "oauth:get" :
                return self::oauth();
            default :
                break;
        }
        switch ($action) {
            case "rwf" :
                return self::rwf();
            case "dms" :
                return self::dms();
            case "mdc" :
                return self::mdc();
            case "rwf_backend" :
                return self::rwf_backend();
            case "swse" :
                return self::swse();
            default :
                break;
        }
        return response("", 404);
	}

	public static function request() {

        Response::setHeaders(["Access-Control-Allow-Origin: *"]);
        $response = Request::method() == "get" ? Proxy::getRequest() : Proxy::postRequest();
        return response($response, 200);
    }

    public static function response() {

        Response::setHeaders(["Access-Control-Allow-Origin: *"]);
        $key = Request::post("key");
        $data = Request::post("data");
        if (!is_null($key) && !is_null($data)) {
            $key .= "_response";
            redis()->RedisString($key)->set($data);
        }
        return response("success", 200);
    }

    public static function power() {

        Response::setHeaders(["Access-Control-Allow-Origin: *"]);
        return response(config("POWER", "on"), 200);
    }

    public static function rwf() {

        Response::setHeaders([
            "Access-Control-Allow-Origin: *",
            "Access-Control-Allow-Headers: access_token, brandId, Content-Type",
            "Access-Control-Allow-Methods: GET, POST, PUT, DELETE, HEAD, OPTIONS"
        ]);
        $host = config("rwf_quality", "");
        $uri = str_replace("/proxy/rwf", "", Request::uri());
        $url = $host . $uri;
        $response = Proxy::dealProxy($url, "quality");
        return response($response);
    }

    public static function dms() {

        Response::setHeaders([
            "Access-Control-Allow-Origin: *",
            "Access-Control-Allow-Headers: access_token, brandId, Content-Type",
            "Access-Control-Allow-Methods: GET, POST, PUT, DELETE, HEAD, OPTIONS"
        ]);
        $host = config("dms_quality", "");
        $uri = str_replace("/proxy/dms", "", Request::uri());
        $url = $host . $uri;
        $response = Proxy::dealProxy($url, "quality");
        return response($response);
    }

    public static function mdc() {

        Response::setHeaders([
            "Access-Control-Allow-Origin: *",
            "Access-Control-Allow-Headers: access_token, brandId, Content-Type",
            "Access-Control-Allow-Methods: GET, POST, PUT, DELETE, HEAD, OPTIONS"
        ]);
        $host = config("mdc_quality", "");
        $uri = str_replace("/proxy/mdc", "", Request::uri());
        $url = $host . $uri;
        $response = Proxy::dealProxy($url, "quality");
        return response($response);
    }

    public static function rwf_backend() {

        Response::setHeaders([
            "Access-Control-Allow-Origin: *",
            "Access-Control-Allow-Headers: access_token, brandId, Content-Type",
            "Access-Control-Allow-Methods: GET, POST, PUT, DELETE, HEAD, OPTIONS"
        ]);
        $host = config("rwf_backend_quality", "");
        $uri = str_replace("/proxy/rwf_backend", "", Request::uri());
        $url = $host . $uri;
        $response = Proxy::dealProxy($url, "quality");
        return response($response);
    }

    public static function swse() {

        $uri = str_replace("/proxy/swse", "", Request::uri());
        Request::setProxyDomain(config("main_domain", "") . "/proxy/swse");
        return Swse::webservice_quality($uri);
    }

    public static function getOauth() {

        return Auth::getOauth();
    }

    public static function oauth() {

        $uri = str_replace("/proxy/oauth", "", Request::uri());
        return Auth::oauth($uri);
    }
}