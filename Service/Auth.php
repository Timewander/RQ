<?php

class Auth {

    public static function getOauth($cache_hit = false) {

        $target = Request::get("redirect", "");
        setcookie("redirect", $target, time() + 100, "/", config("cookie_domain", ""));
        $url = config("oauth_resource", "");
        $response = $cache_hit ? self::getCache($url) : Http::get($url);
        return response("", 200, ["Location" => $response]);
    }

    public static function oauth($uri) {

        $target = Request::cookie("redirect", "default");
        $mapping = config("OAUTH_REDIRECT_MAP", []);
        $domain = isset($mapping[$target]) && !empty($mapping[$target]) ? $mapping[$target] : config("main_domain");
        $url = $domain . $uri;
        return response("", 200, ["Location" => $url]);
    }

    private static function getCache($url) {

        $redis = new RedisBase();
        $result = $redis->RedisString($url)->get();
        if ($result === false) {
            $result = Http::get($url);
            $redis->RedisString($url)->set($result);
            $redis->expire($url, config("OAUTH_CACHE_TTL", 86400));
        }
        return $result;
    }
}