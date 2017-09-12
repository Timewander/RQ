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

        $result = json_decode(Proxy::postRequest($payload), true);
        if (isset($result["faultstring"])) {
            throw new SoapFault($result["faultcode"], $result["faultstring"], null, a2o($result["detail"]));
        }
        return $result;
    }
}