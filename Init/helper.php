<?php

function config($key, $default = null) {

    return Config::get($key, $default);
}

function redis() {

    return new RedisBase();
}

function response($content = "", $http_code = 200, $header = []) {

    Response::build($content, $http_code, $header);
    return null;
}