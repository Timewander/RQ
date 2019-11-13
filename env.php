<?php

$env = [
    "REDIS_HOST" => "localhost",
    "REDIS_PORT" => 6379,
    "REDIS_PASSWORD" => "d1m123456",

    "WSDL_CACHE" => false,
    "WSDL_CACHE_TTL" => 3600,

    "OAUTH_REDIRECT_MAP" => [
        "default" => "",
        "dinner" => "http://proxy-sky.richemont.d1miao.com/dinner/oauth_dinner",
        "weboutique" => "http://proxy-sky.richemont.d1miao.com/proxy/oauth_test",
    ],
    "OAUTH_CACHE_TTL" => 8640000,

    "POWER" => "on",

    "WECHAT_TOKEN" => "Timewander",
    "WECHAT_APP_ID" => "wxb727538a75642949",
    "WECHAT_APP_SECRET" => "5192a55e97907c2dc18d846f31382746",
    "WECHAT_AES_KEY" => "mu3LEcC67yqQmn3299ZYuiZwpp6fS679gFNmqJqdbHo=",
//    "WECHAT_TOKEN" => "richemont", // d1m
//    "WECHAT_APP_ID" => "wx59cd645c368d1d88", // d1m
//    "WECHAT_APP_SECRET" => "2459439d26721f9703eeedc403cb050c", // d1m
//    "WECHAT_AES_KEY" => "sFEYLUG8qy2wGseNMvZOrU40ddpX9dssuTlY2tKIAiM=", // d1m
//    "WECHAT_APP_ID" => "wxaf76be79ce78a751", // ric
//    "WECHAT_APP_SECRET" => "10b2abeecc7af512b8e359e5cf1b240e", // ric
];

