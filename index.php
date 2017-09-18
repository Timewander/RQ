<?php

include "env.php";
include "init.php";

Portal::route();

$control = Request::control();
if (!is_null($control)) {
    $action = "handler";
    if (class_exists($control) && method_exists(new $control(), $action)) {
        return $control::$action();
    }
    return response("", 404);
}