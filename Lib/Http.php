<?php

class Http {

    public static function init() {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        return $ch;
    }

	public static function network($url, $header = [], $method = "get", $payload = "", $timeout = 60) {

		$ch = self::init();
        switch (strtoupper($method)) {
            case "POST" :
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                break;
            case "PUT" :
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                break;
            case "DELETE" :
            case "OPTIONS" :
            case "HEAD" :
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$file_contents = curl_exec($ch);
		curl_close($ch);
		return $file_contents;
	}

	public static function get($url, $header = []) {

		return self::network($url, $header, "get", "", 10);
	}

	public static function post($url, $payload, $header = []) {

        list($payload, $header) = self::setPayload($payload, $header);
		return self::network($url, self::setHeader($header), "post", $payload, 10);
	}

	public static function put($url, $payload, $header = []) {

        list($payload, $header) = self::setPayload($payload, $header);
		return self::network($url, self::setHeader($header), "put", $payload, 10);
	}

	public static function call($url, $method, $header = []) {

        return self::network($url, self::setHeader($header), $method, "", 10);
    }

    private static function setPayload($payload, $header) {

        if (!isset($header["Content-Type"])) {
            if (is_string($payload) && is_array(json_decode($payload, true))) {
                $header["Content-Type"] = "application/json";
            } else {
                $header["Content-Type"] = "application/x-www-form-urlencoded";
            }
        }
        if (is_array($payload) || is_object($payload)) {
            if (strpos($header["Content-Type"], "application/json") !== false) {
                $payload = json_encode($payload);
            } else {
                $payload = self::urlencoded($payload);
            }
        }

        return [$payload, $header];
    }

    public static function setHeader($header) {

        $result = [];
        foreach ($header as $key => $value) {
            $result[] = "$key: $value";
        }
        return $result;
    }

	public static function urlencoded($body) {

        $params = [];
        foreach ($body as $key => $val) {
            $params[] = $key . "=" . urlencode($val);
        }
        return join("&", $params);
    }
}