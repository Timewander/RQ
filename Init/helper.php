<?php

function config($key, $default = null) {

    return Config::get($key, $default);
}

function redis() {

    return new RedisBase();
}