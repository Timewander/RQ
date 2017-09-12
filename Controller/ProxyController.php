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
            default :
                break;
        }
        switch ($action) {
            case "rwf" :
                return self::rwf();
            case "rwf_backend" :
                return self::rwf_backend();
            case "swse" :
                return self::swse();
            default :
                break;
        }
        return null;
	}

	public static function request() {

        header("Access-Control-Allow-Origin: *");
        $response = Request::method() == "get" ? Proxy::getRequest() : Proxy::postRequest();
        echo $response;
        return null;
	}

    public static function response() {

        header("Access-Control-Allow-Origin: *");
        $key = Request::post("key");
        $data = Request::post("data");
        if (!is_null($key) && !is_null($data)) {
            $key .= "_response";
            redis()->RedisString($key)->set($data);
        }
        return "success";
    }

    public static function power() {

        header("Access-Control-Allow-Origin: *");
        echo config("POWER", "on");
        return null;
    }

    public static function rwf() {

        $host = "https://wechat-framework-quality.intranet.rccad.net";
        $uri = str_replace("/proxy/rwf", "", Request::uri());
        $url = $host . $uri;
        $response = self::dealProxy($url);
        echo $response;
        return null;
    }

    public static function rwf_backend() {

        $host = "https://wechat-quality.intranet.rccad.net/backend";
        $uri = str_replace("/proxy/rwf_backend", "", Request::uri());
        $url = $host . $uri;
        $response = self::dealProxy($url);
        echo $response;
        return null;
    }

    public static function swse() {

        $uri = str_replace("/proxy/swse", "", Request::uri());
        return Swse::webservice_quality($uri);
    }

    private static function dealProxy($url) {

        $payload = self::buildPayload($url);
        $response = Proxy::postRequest($payload);
        $resource = self::getResource();
        $type = substr($url, -4);
        if (isset($resource[$type])) {
            $response = base64_decode($response);
            header("Content-Type: " . $resource[$type]);
        }
        return $response;
    }

    private static function buildPayload($url) {

        return [
            "url" => $url,
            "body" => Request::payload(),
            "method" => Request::method(),
            "header" => Http::setHeader(Request::headers()),
        ];
    }

    private static function getResource() {

        return [
            ".jpg" => "image/jpeg",
            ".png" => "image/png",
            ".gif" => "image/gif",
            ".mp3" => "audio/mp3",
            ".amr" => "audio/amr",
            ".avi" => "video/avi",
            ".mp4" => "video/mpeg4",
        ];
    }

}