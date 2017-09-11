<?php

class SwseHandler {

    public static $url;
    public static $usr;
    public static $psw;

    public function __call($name, $params) {

        $args = [
            "username" => self::$usr,
            "password" => self::$psw,
            "location" => self::$url,
            "function" => $name,
        ];
        $payload = [
            "url" => self::$url . "?wsdl",
            "body" => $params,
            "method" => "soap",
            "header" => $args,
        ];

        $result = json_decode(Proxy::postRequest($payload));
        if (isset($result["proxy_message"])) {
            throw new SoapFault($result["proxy_code"], $result["proxy_message"]);
        }
        return $result;
    }
}