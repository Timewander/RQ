<?php

trait Card {

    public static function test() {
        echo "<pre>";

        $old_cards = json_decode('["pl_uptxiF2PicXWwghPtL2EFtGfQ","pl_upt1ZkDKHWI3CBVEVJRo_iNzU","pl_upt2twFWhUi4ilFr2jCdEBm0o","pl_upt1bwNhpyv9lZ1hnDz50Noco","pl_uptzFywrsrtBbbb6z4tVDS8Wo","pl_uptztgZCU-0AKx53k2RaSmWuU","pl_upt_79wDJAWrunbHx8m1282RQ"]');

        $token = self::token(true);
        $url = "https://api.weixin.qq.com/card/batchget?access_token=$token";
        $data = [
            "offset" => 0,
            "count" => 50,
            "status_list" => [
                "CARD_STATUS_NOT_VERIFY",
                "CARD_STATUS_VERIFY_OK",
                "CARD_STATUS_VERIFY_FAIL",
                "CARD_STATUS_DELETE",
                "CARD_STATUS_DISPATCH",
            ]
        ];
        $res = Http::post($url, $data, ["Content-Type" => "application/json"]);
        $res_cards = json_decode($res, true);
        $res_cards = array_diff($res_cards["card_id_list"], $old_cards);
        if (empty($res_cards) && false) {
            $image = "http://mmbiz.qpic.cn/mmbiz_jpg/BhoQxo0TrlVmlsIO2qh7W3tf5vJngQfDrNBlPP302AZ4ZJ3IOIABLjuzroE9pAomAibmOib1daib31JkW2kFkOYvg/0";
            $color = "Color030";
            $url = "https://api.weixin.qq.com/card/create?access_token=$token";
            $data = [
                "card" => [
                    "card_type" => "GIFT",
                    "gift" => [
                        "base_info" => [
                            "logo_url" => $image,
                            "code_type" => "CODE_TYPE_TEXT",
                            "brand_name" => "黑店",
                            "title" => "感恩节送Barry",
                            "color" => $color,
                            "notice" => "Barry具有一定攻击性，轻拿轻放",
                            "description" => "仅有一百只，先到先得",
                            "sku" => [
                                "quantity" => 100,
                            ],
                            "date_info" => [
                                "type" => "DATE_TYPE_FIX_TIME_RANGE",
                                "begin_timestamp" => strtotime("20171122"),
                                "end_timestamp" => strtotime("20171230"),
                            ],

                            /** default null able **/
                            "use_custom_code" => false,
                            "bind_openid" => false,
                            "can_share" => true,
                            "can_give_friend" => true,
                            "use_all_locations" => true,
                            "service_phone" => "110",
                            "get_limit" => "10",
                            "use_limit" => "10",

                            "center_title" => "Barry兑换券",
                            "center_sub_title" => "走过路过不要错过",
                            "center_url" => "http://proxy-sky.richemont.d1m.cn/wechat/buy",
//                            "center_app_brand_user_name" => "gh_d0ab2b97a70d@app",
//                            "center_app_brand_pass" => "",

                            "custom_url_name" => "买买买",
                            "custom_url" => "http://proxy-sky.richemont.d1m.cn/wechat/buy",
//                            "custom_app_brand_user_name" => "gh_d0ab2b97a70d@app",
//                            "custom_app_brand_pass" => "",
                            "custom_url_sub_title" => "Go",

                            "promotion_url_name" => "介绍",
                            "promotion_url" => "http://proxy-sky.richemont.d1m.cn/wechat/buy",
//                            "promotion_app_brand_user_name" => "gh_d0ab2b97a70d@app",
//                            "promotion_app_brand_pass" => "",
                            "promotion_url_sub_title" => "马上兑换",
                            /** default null able **/

                        ],
                        "advanced_info" => [

                        ],
                        "gift" => "兑换Barry一个",
                    ]
                ]
            ];
            $res = Http::post($url, json_encode($data, JSON_UNESCAPED_UNICODE), ["Content-Type" => "application/json"]);
            $res_create = json_decode($res, true);
            var_dump($res_create["card_id"]);
        }
        var_dump($res_cards);
        foreach ($res_cards as $card_id) {
            $url = "https://api.weixin.qq.com/card/get?access_token=$token";
            $data = [
                "card_id" => $card_id,
            ];
            $res = Http::post($url, $data, ["Content-Type" => "application/json"]);
            $res_card = json_decode($res, true);
            var_dump($res_card["card"]);
            $url = "https://api.weixin.qq.com/card/qrcode/create?access_token=$token";
            $data = [
                "action_name" => "QR_CARD",
                "expire_seconds" => 1800,
                "action_info" => [
                    "card" => [
                        "card_id" => $card_id,
                        "is_unique_code" => true,
                    ],
                ],
            ];
//            $res = Http::post($url, $data, ["Content-Type" => "application/json"]);
//            $res_qr = json_decode($res, true);
//            var_dump($res_qr);
        }
        $url = "https://api.weixin.qq.com/card/update?access_token=$token";
        $data = [
            "card_id" => "pl_upt5eawzcs1k295LBw4O7es2E",
            "gift" => [
                "base_info" => [
                    "title" => "D1M Barry兑换券",
                    "center_title" => "前往兑换",
                    "description" => "仅有一百个，先到先得",
                    "promotion_url_name" => "买买买 * 2",
                    "promotion_url_sub_title" => "Go * 2",
                    "center_app_brand_user_name" => null,
                    "center_url" => "http://proxy-sky.richemont.d1m.cn/wechat/buy?from=BT",
                    "custom_url" => "http://proxy-sky.richemont.d1m.cn/wechat/buy?from=Go",
                    "promotion_url" => "http://proxy-sky.richemont.d1m.cn/wechat/buy?from=Go2",
                ],
            ],
        ];
//        $res = Http::post($url, json_encode($data, JSON_UNESCAPED_UNICODE), ["Content-Type" => "application/json"]);
//        $res_update = json_decode($res, true);
//        var_dump($res_update);
    }
}