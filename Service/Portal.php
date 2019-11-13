<?php

class Portal {

    public static function route() {

        $host = Request::domain();
        $action = strtolower(substr(Request::control(), 0, -10));
        $domain = config("portal_domain", "");
        switch ("http://$host/$action") {
            case "$domain/webservices" :
                Request::setProxyDomain("$domain/webservices");
                $uri = str_replace("/webservices", "", Request::uri());
                Swse::webservice_quality($uri);
                break;
            case "$domain/srvswse" :
                Swse::rest_quality();
                break;
            case "$domain/commerce" :
                ProxyController::commerce();
                break;
        }
    }
}