<?php

class Proxy {

    public static function getRequest() {

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
        return response("", 404);
    }

    public static function postRequest($data = null) {

        $data = is_null($data) ? Request::post("data") : $data;
        $redis = new RedisBase();
        if (is_array($data) && isset($data["url"]) && isset($data["body"]) && isset($data["header"])) {
            $key = $redis->RedisString("proxy_list_index")->incr();
            $redis->RedisString($key)->set(json_encode($data));
            $redis->RedisList("proxy_request_list")->add($key);
            $try = 0;
            $key .= "_response";
            while ($try < 1000) {
                usleep(50000);
                $res = $redis->RedisString($key)->get();
                if ($res !== false) {
                    $redis->del($key);
                    return $res;
                }
                $try ++;
            }
            return response("", 408);
        }
        return response("", 400);
    }

    public static function dealProxy($url, $env) {

        $payload = self::buildPayload($url, $env);
        $response = self::postRequest($payload);
        $resource = self::getResource();
        $type = substr($url, -4);
        if (isset($resource[$type])) {
            $response = base64_decode($response);
            header("Content-Type: " . $resource[$type]);
        }
        return $response;
    }

    private static function buildPayload($url, $env) {

        $header = Request::headers();
        $header["Host"] = str_replace("sky", $env, $header["Host"]);
        return [
            "url" => $url,
            "body" => Request::payload(),
            "method" => Request::method(),
            "header" => Http::setHeader($header),
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