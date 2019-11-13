<?php
die;
$client = new SoapClient("http://dev-sky.richemont.d1miao.com/webservices/OrderEJB?wsdl", [
    "login" => "swseCartierQual",
    "password" => "swseq@car2015",
    "cache_wsdl" => WSDL_CACHE_BOTH,
]);
$count = $success = 0;
$time_start = intval(microtime(true) * 1000);
while ($count < 1000) {
    $time_in = intval(microtime(true) * 1000);
    $count ++;
    try {
        $res = $client->getSwseStatus([]);
        if (isset($res->return) && $res->return === true) {
            $success ++;
        }
    } catch (SoapFault $fault) {

    }
    $time_out = intval(microtime(true) * 1000);
    $delta = $time_out - $time_in;
    $average = intval(($time_out - $time_start) / $count);
    echo "$success/$count usage:$delta ms average:$average ms\n";
}

