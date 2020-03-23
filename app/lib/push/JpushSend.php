<?php
// +----------------------------------------------------------------------
// | sxr.ijiandian.com [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/6/26
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\lib\push;


class JpushSend
{
    private $app_key = 'ac8253b73d0b616f246f84fd';        //待发送的应用程序(appKey)，只能填一个。
    private $master_secret = 'a0d339d49cc881462a7424bf';    //主密码
    private $url = "https://api.jpush.cn/v3/push";      //推送的地址

    public function __construct($app_key = null, $master_secret = null, $url = null)
    {
        if ($app_key) $this->app_key = $app_key;
        if ($master_secret) $this->master_secret = $master_secret;
        if ($url) $this->url = $url;
    }

    public function send_pub($receiver, $push_data, $extras, $m_time=86400)
    {
        $message = "";//存储推送状态
        $result = $this->push($receiver, $push_data, $extras, $m_time);
        if ($result) {
            $res_arr = json_decode($result, true);
            if (isset($res_arr['error'])) {
                echo $res_arr['error']['message'];
                $error_code = $res_arr['error']['code'];
                switch ($error_code) {
                    case 200:
                        $message = '发送成功！';
                        break;
                    case 1000:
                        $message = '失败(系统内部错误)';
                        break;
                    case 1001:
                        $message = '失败(只支持 HTTP Post 方法，不支持 Get 方法)';
                        break;
                    case 1002:
                        $message = '失败(缺少了必须的参数)';
                        break;
                    case 1003:
                        $message = '失败(参数值不合法)';
                        break;
                    case 1004:
                        $message = '失败(验证失败)';
                        break;
                    case 1005:
                        $message = '失败(消息体太大)';
                        break;
                    case 1008:
                        $message = '失败(appkey参数非法)';
                        break;
                    case 1020:
                        $message = '失败(只支持 HTTPS 请求)';
                        break;
                    case 1030:
                        $message = '失败(内部服务超时)';
                        break;
                    default:
                        $message = '失败(返回其他状态，目前不清楚额，请联系开发人员！)';
                        break;
                }
            } else {
                $message = "success";
            }
        } else {
            $message = 'fail';
        }
        return $message;
    }

    public function push($receiver, $push_data, $extras, $m_time)
    {
        $base64 = base64_encode("$this->app_key:$this->master_secret");
        $header = array("Authorization:Basic $base64", "Content-Type:application/json");
        $data = array();
        $data['platform'] = 'all';
        if ($receiver == 'all') {
            $data['audience'] = $receiver;
        }else{
            $data['audience']['alias'] = $receiver;
        }

        $data['notification'] = array(
            "alert"=>$push_data,
            "android" => array(
                "alert" => $push_data['title'],
                "title" => $push_data['content'],
                "builder_id" => 1,
                "extras" => $extras
            ),
            "ios" => array(
                "alert" => $push_data['title'],
                "sound" => "default",
                "badge" => "+1",
                "content-available" => true,
                "mutable-content" => true,
                "extras" => $extras
            )
        );

        $data['message'] = array(
            "msg_content" => $push_data['content'],
            'title' => $push_data['title'],
            'content_type' => 'text',
            "extras" => $extras
        );

        $data['options'] = array(
            "sendno" => time(),
            "time_to_live" => $m_time,
            "apns_production" => false,
        );
        $param = json_encode($data);
        $res = $this->push_curl($param, $header);

        if ($res) {
            return $res;
        } else {
            return false;
        }
    }

    public function push_curl($param = "", $header = "")
    {
        if (empty($param)) {
            return false;
        }
        $postUrl = $this->url;
        $curlPost = $param;
        $ch = curl_init();                                      
        curl_setopt($ch, CURLOPT_URL, $postUrl);                
        curl_setopt($ch, CURLOPT_HEADER, 0);                    
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);           
        curl_setopt($ch, CURLOPT_POST, 1);                      
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);           
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);       
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

}