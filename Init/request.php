<?php

$gets = $_GET;
if (isset($_SERVER["CONTENT_TYPE"]) && strpos($_SERVER["CONTENT_TYPE"], "application/json") !== false) {
    $raw_post_data = file_get_contents('php://input', 'r');
    $posts = json_decode($raw_post_data, true);
} else {
    $posts = $_POST;
}

$path = [];
$rule = ["P_C", "P_A", "P_P1", "P_P2", "P_P3"];
foreach ($rule as $item) {
    if (isset($gets[$item])) {
        $path[$item] = $gets[$item];
    } else {
        break;
    }
}
$gets = array_diff_key($gets, $path);

Request::setPath(array_values($path));
Request::setParams($gets);
Request::setPayload($posts);
Request::setMethod($_SERVER["REQUEST_METHOD"]);
Request::setHeader(apache_request_headers());