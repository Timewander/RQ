<?php

define("ROOT_DIR", __DIR__);
define("ENV", __DIR__ . "/env.php");
define("RESOURCE", __DIR__ . "/resource.php");
date_default_timezone_set("Asia/Shanghai");

include "Init/autoload.php";
include "Init/function.php";
include "Init/helper.php";
include "Init/request.php";
