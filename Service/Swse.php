<?php

class Swse {

    private static function wsdl($url) {

        $payload = [
            "url" => $url,
            "body" => Request::payload(),
            "method" => Request::method(),
            "header" => Http::setHeader(Request::headers()),
        ];
        $response = Proxy::postRequest($payload);
        header("Content-Type: text/xml;charset=UTF-8");
        echo $response;
        return null;
    }

    public static function webservice_quality($uri) {

        $host = "https://swset-cn-cartier-quality.intranet.rccad.net:8443/webservices";
        $url = $host . $uri;
        if (!is_null(Request::get("wsdl"))) {
           return self::wsdl($url);
        }
        SwseHandler::$url = $url;
        SwseHandler::$usr = "swseCartierQual";
        SwseHandler::$psw = "swseq@car2015";

        $server = new SoapServer("http://" . Request::domain() . Request::uri() . "?wsdl", []);
        $server->setClass("SwseHandler");
        $server->handle();
        return null;
    }
}