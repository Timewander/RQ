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
            default :
                break;
        }
        return null;
	}

	public static function request() {

        $response = Request::method() == "get" ? self::getRequest() : self::postRequest();
        echo $response;
        return null;
	}

    private static function getRequest() {

        header("Access-Control-Allow-Origin: *");
        $redis = new RedisBase();
        $key = $redis->RedisList("proxy_request_list")->pop();
        if ($key !== false) {
            $req = $redis->RedisString($key)->get();
            $redis->del($key);
            $res = json_encode([
                "key" => $key,
                "req" => json_decode($req),
            ]);
            return $res;
        }
        return "";
    }

    private static function postRequest($data = null) {

        header("Access-Control-Allow-Origin: *");
        $data = is_null($data) ? Request::post("data") : $data;
        $redis = new RedisBase();
        if (is_array($data) && isset($data["url"]) && isset($data["body"]) && isset($data["header"])) {
            $key = $redis->RedisString("proxy_list_index")->incr();
            $redis->RedisString($key)->set(json_encode($data));
            $redis->RedisList("proxy_request_list")->add($key);
            $try = 0;
            $key .= "_response";
            while ($try < 600) {
                usleep(50000);
                $res = $redis->RedisString($key)->get();
                if ($res !== false) {
                    $redis->del($key);
                    return $res;
                }
                $try ++;
            }
        }
        return "";
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
        $uri = substr($_SERVER["REQUEST_URI"], 10);
        $url = $host . $uri;
        $response = self::dealProxy($url);
        echo $response;
        return null;
    }

    public static function rwf_backend() {

        $host = "https://wechat-quality.intranet.rccad.net/backend";
        $uri = substr($_SERVER["REQUEST_URI"], 18);
        $url = $host . $uri;
        $response = self::dealProxy($url);
        echo $response;
        return null;
    }

    private static function dealProxy($url) {

        $payload = self::buildPayload($url);
        $response = self::postRequest($payload);
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