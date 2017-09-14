<?php

class Auth {

    public static function getOauth() {

        $target = Request::get("redirect", "");
        setcookie("redirect", $target, time() + 100, "/", config("cookie_domain", ""));
        $url = config("oauth_resource", "");
        $response = Http::get($url);
        header("Location: $response");
        return null;
    }

    public static function oauth($uri) {

        $target = Request::cookie("redirect", "default");
        $mapping = config("OAUTH_REDIRECT_MAP", []);
        $domain = isset($mapping[$target]) && !empty($mapping[$target]) ? $mapping[$target] : config("main_domain");
        $url = $domain . $uri;
        header("Location: $url");
        return null;
    }
}