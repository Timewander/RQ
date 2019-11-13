<?php

class WechatController {

    public static function handler() {
        $action = Request::action();
        $method = Request::method();
        switch ("$action:$method") {
            case "buy:get" :
                return self::buy();
            case "white_list:get" :
                return self::white_list();
            case "token:get" :
                return self::token();
            default :
                break;
        }
        switch ($action) {
            case "notify" :
                return self::notify();
            default :
                break;
        }
        return response("", 404);
    }

    public static function buy() {

        error_log(json_encode(Request::all()));
        $from = Request::get("from");
        $html = "<title>From $from</title>
<meta name='viewport' content='width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no'>
<meta name='author' content='Timewander, gf-fly@163.com'>
<style>
    div {
        width: 100%;
        text-align: center;
        font-size: 1em;
        line-height: 2em;
        margin-top: 2em;
    }
</style>
<div>Barry还在闹情绪<br>晚点再来兑换吧</div>";
        return response($html);
    }

    public static function white_list() {

        $token = self::token(true);
        $url = "https://api.weixin.qq.com/card/testwhitelist/set?access_token=$token";
        $data = [
            "openid" => [
                "ol_uptxabLPsfsz1WBikJrbwDVTw",
            ],
        ];
        $res = Http::post($url, $data, ["Content-Type" => "application/json"]);
        return response($res);
    }


    public static function token($inner = false) {

        $redis = new RedisString("access_token");
        $token = $redis->get();
        if ($token === false) {
            $result = Wechat::accessToken();
            if ($result !== false) {
                $token = $result["access_token"];
                $expire = $result["expires_in"];
                $redis->set($token);
                redis()->expire("access_token", intval($expire / 2));
            }
        }

        if ($inner) {
            return $token;
        }
        return response($token);
    }

    public static function notify() {

        $raw_post_data = file_get_contents('php://input', 'r');
        Request::setPayload(Xml::stringToArray($raw_post_data));
        $data = Request::all();
        log_info("notify", json_encode($data));
        $signature = Request::get("signature", "");
        $timestamp = Request::get("timestamp", "");
        $nonce = Request::get("nonce", "");
        if (empty($signature) || Wechat::signature($timestamp, $nonce) !== $signature) {
            return response("", 404);
        }
        $echostr = Request::get("echostr", "");
        if (!empty($echostr)) {
            return response($echostr);
        }

        $encrypt_msg = Request::post("Encrypt", "");
        $msg_signature = Request::get("msg_signature", "");
        if (empty($msg_signature) || Wechat::msgSignature($timestamp, $nonce, $encrypt_msg) !== $msg_signature) {
            return response("", 404);
        }

        $decrypt = Wechat::decryptMsg($encrypt_msg);
        if ($decrypt === false) {
            return response("", 404);
        }
        $from = $decrypt["FromUserName"];
        $to = $decrypt["ToUserName"];
        $type = $decrypt["MsgType"];
        $time = $decrypt["CreateTime"];
        if (in_array($type, ["text"])) {
            $content = $decrypt["Content"];
            if (trim($content) !== "Barry") {
                $content = "请输入“Barry”领取礼品卡";
                $reply_msg = Wechat::buildReplyMsg($from, $to, $timestamp, $content);
            } else {
                $msgId = $decrypt["MsgId"];
                $deal = redis()->RedisString($from)->get();
                if ($deal == $msgId) {
                    return response("success");
                }
                redis()->RedisString($from)->set($msgId);
                $token = self::token(true);
                $url = "https://api.weixin.qq.com/card/qrcode/create?access_token=$token";
                $data = [
                    "action_name" => "QR_CARD",
                    "expire_seconds" => 1800,
                    "action_info" => [
                        "card" => [
                            "card_id" => "pl_upt5eawzcs1k295LBw4O7es2E",
                            "is_unique_code" => true,
                        ],
                    ],
                ];
                $res = Http::post($url, $data, ["Content-Type" => "application/json"]);
                $res_qr = json_decode($res, true);
                $qr = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=" . urlencode($res_qr["ticket"]);
                $pic = "http://mmbiz.qpic.cn/mmbiz_jpg/BhoQxo0TrlVmlsIO2qh7W3tf5vJngQfDjYiaEbRk1LBKwFlFXcWicPF8pS5v3y5lnyHfF7z3gFhDIFAib3LyHI2yw/0";
                $title = "免费领取Barry兑换券";
                $sub_title = "马上领取(打开后请识别二维码)";
                $reply_msg = Wechat::buildReplyNews($from, $to, $timestamp, $title, $sub_title, $pic, $qr);
            }
        } elseif (in_array($type, ["event"])) {
            if ($decrypt["Event"] == "subscribe") {
                $content = "请输入“Barry”领取礼品卡";
                $reply_msg = Wechat::buildReplyMsg($from, $to, $timestamp, $content);
            } else {
                return response("success");
            }
        } else {
            return response("success");
        }

        $encrypt = Wechat::encryptMsg($reply_msg);
        $msg_signature = Wechat::msgSignature($timestamp, $nonce, $encrypt);
        $reply = Wechat::buildReply($encrypt, $msg_signature, $timestamp, $nonce);
        return response($reply, 200, ["Content-Type: text/xml"]);
    }
}