<?php

class Wechat {

    public static function signature($timestamp, $nonce) {

        $params = [config("WECHAT_TOKEN"), $timestamp, $nonce];
        sort($params, SORT_STRING);
        $hashcode = sha1(join("", $params));
        return $hashcode;
    }

    public static function msgSignature($timestamp, $nonce, $encrypt_msg) {

        $params = [config("WECHAT_TOKEN"), $timestamp, $nonce, $encrypt_msg];
        sort($params, SORT_STRING);
        $hashcode = sha1(join("", $params));
        return $hashcode;
    }

    public static function decryptMsg($encrypt_msg) {

        $aes_key = base64_decode(config("WECHAT_AES_KEY", ""));
        $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_RAND);
        mcrypt_generic_init($module, $aes_key, $iv);

        $decrypted = mdecrypt_generic($module, base64_decode($encrypt_msg));
        mcrypt_generic_deinit($module);
        mcrypt_module_close($module);

        $pad = ord(substr($decrypted, -1));
        $content = substr($decrypted, 16, -1 * $pad);
        if ($content == false) {
            return false;
        }

        $xml_length = current(unpack("N", substr($content, 0, 4)));
        $xml_content = substr($content, 4, $xml_length);
        $from_appId = substr($content, $xml_length + 4);
        if ($from_appId !== config("WECHAT_APP_ID", "")) {
            return false;
        }

        return Xml::stringToArray($xml_content);
    }

    public static function encryptMsg($msg) {

        $msg = str_repeat(chr(rand(48, 57)), 16) . pack("N", strlen($msg)) . $msg . config("WECHAT_APP_ID", "");
        $aes_key = base64_decode(config("WECHAT_AES_KEY", ""));
        $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $pad = $size - (strlen($msg) % $size);
        $msg .= str_repeat(chr($pad), $pad);

        $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_RAND);
        mcrypt_generic_init($module, $aes_key, $iv);

        $encrypt = base64_encode(mcrypt_generic($module, $msg));
        mcrypt_generic_deinit($module);
        mcrypt_module_close($module);

        return $encrypt;
    }

    public static function buildReplyMsg($from, $to, $timestamp, $content) {

        $s = "<![CDATA[%s]]>";
        $template = "<xml><ToUserName>$s</ToUserName><FromUserName>$s</FromUserName><CreateTime>%s</CreateTime><MsgType>$s</MsgType><Content>$s</Content></xml>";
        return sprintf($template, $from, $to, $timestamp, "text", $content);
    }

    public static function buildReplyNews($from, $to, $timestamp, $title, $sub_title, $image, $url) {

        $s = "<![CDATA[%s]]>";
        $template = "<xml><ToUserName>$s</ToUserName><FromUserName>$s</FromUserName><CreateTime>%s</CreateTime><MsgType>$s</MsgType><ArticleCount>1</ArticleCount><Articles><item><Title>$s</Title><Description>$s</Description><PicUrl>$s</PicUrl><Url>$s</Url></item></Articles></xml>";
        return sprintf($template, $from, $to, $timestamp, "news", $title, $sub_title, $image, $url);
    }

    public static function buildReply($encrypt, $signature, $timestamp, $nonce) {

        $s = "<![CDATA[%s]]>";
        $template = "<xml><Encrypt>$s</Encrypt><MsgSignature>$s</MsgSignature><TimeStamp>%s</TimeStamp><Nonce>$s</Nonce></xml>";
        return sprintf($template, $encrypt, $signature, $timestamp, $nonce);
    }

    public static function accessToken() {

        $appId = config("WECHAT_APP_ID", "");
        $secret = config("WECHAT_APP_SECRET", "");
        $response = Http::get("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appId&secret=$secret");
        $result = json_decode($response, true);
        if (!empty($result) && isset($result["access_token"]) && isset($result["expires_in"])) {
            return $result;
        } else {
            error_log($response);
            return false;
        }
    }
}