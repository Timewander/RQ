<?php

function remote_ip() {
    return $_SERVER["REMOTE_ADDR"];
}

function o2a($stdClass) {
    if (is_scalar($stdClass)) {
        return $stdClass;
    }
    $ret = [];
    foreach ($stdClass as $key => $val) {
        if (($val instanceof stdClass) || is_array($val)) {
            $ret[$key] = o2a($val);
        } else {
            $ret[$key] = $val;
        }
    }
    return $ret;
}

function a2o($array) {
    if (is_scalar($array) || empty($array)) {
        return $array;
    }
    $ret = new stdClass();
    foreach ($array as $key => $val) {
        if (is_array($val) || ($val instanceof stdClass)) {
            $ret->$key = a2o($val);
        } else {
            $ret->$key = $val;
        }
    }
    return $ret;
}

function skipXSS($data) {
    if (is_array($data)) {
        foreach ($data as $key => $val) {
            $data[$key] = skipXSS($val);
        }
    } else if ($data instanceof stdClass) {
        foreach ($data as $key => $val) {
            $data->$key = skipXSS($val);
        }
    } else if (is_string($data)) {
        $data = str_replace("'", '', $data);
        $data = str_replace("\"", '', $data);
        $data = str_replace("\\\\", '', $data);
    }
    return $data;
}

function isMobile() {
    $user_agent = $_SERVER["HTTP_USER_AGENT"];
    $mobile_list = "phone|pad|pod|iPhone|iPod|ios|iPad|Android|Mobile|IEMobile|MQQBrowser|JUC|Fennec|wOSBrowser|BrowserNG|WebOS|Windows Phone";
    foreach (explode("|", $mobile_list) as $item) {
        if (strpos($user_agent, $item) !== false) {
            return true;
        }
    }
    return false;
}
