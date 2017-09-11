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
        return "";
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
}