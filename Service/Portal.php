<?php

class Portal {

    public static function route($host) {

        $action = strtolower(substr(Request::control(), 0, -10));
        switch ("$host/$action") {
            case "dev-sky.richemont.d1m.cn/webservices" :
                $uri = str_replace("/webservices", "", Request::uri());
                return Swse::webservice_quality($uri);
            case "dev-sky.richemont.d1m.cn/srvswse" :
                return Swse::rest_quality();
            default :
                return null;
        }
    }

}