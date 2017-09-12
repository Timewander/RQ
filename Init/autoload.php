<?php

$need = [];
foreach ($need as $path) {
    if (file_exists(ROOT_DIR . "/$path")) {
        require_once($path);
    }
}

function __autoload($className) {
    $paths = ["Lib/", "Service/", "Controller/", "Handler/", ""];
    foreach ($paths as $path) {
        $classpath = ROOT_DIR . "/{$path}{$className}.php";
        if (file_exists($classpath)) {
            require_once($classpath);
            break;
        }
    }
}
