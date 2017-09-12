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
                return Swse::webservice_quality($uri);
            case "$domain/srvswse" :
                return Swse::rest_quality();
            default :
                return null;
        }
    }

}