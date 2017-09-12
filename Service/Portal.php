<?php

class Portal {

    public static function route($host) {

        switch ($host) {
            case "dev-sky.richemont.d1m.cn" :
                return ProxyController::swse();
            default :
                return null;
        }
    }
}