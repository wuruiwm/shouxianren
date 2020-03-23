<?php
// +----------------------------------------------------------------------
// | sxr.ijiandian.com [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/6/24
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------
namespace app\lib\push;
require_once  EXTEND_PATH . 'jpush/autoload.php';

use JPush\Client as JPush;
class JPushs
{
    private $app_key;
    private $master_secret;
    private $client;

    public function __construct(){

        $this->app_key        = "ac8253b73d0b616f246f84fd";
        $this->master_secret  = "a0d339d49cc881462a7424bf";
        $this->client         = new JPush($this->app_key, $this->master_secret);

    }
    /**
     * 发送给所有人
     * @param $push_data // 发送内容 含标题和内容
     * @param $push_param // 带额外参数 前端接收 做处理
     * @return array
     */
    public function pushMessageALL($push_data,$push_param){
        $result = $this->client->push()
            ->setPlatform("all") // 推送设备，all=>所有用户，
            ->addAllAudience() // 推送给所有人 选其一
            ->addAndroidNotification($push_data['content'], $push_data['title'], 1,$push_param)
            ->iosNotification(['sound' => 'default']) // 设置提示声音 空为默认声音
            ->addIosNotification($push_data['content'], $push_data['title'], '+1', true, 'iOS category', $push_param)
            ->send();
        return $result;
    }

    /**
     * 按照别名进行发送
     * @param $push_alias // 按别名发送 数组格式
     * @param $push_data // 发送内容 含标题和内容
     * @param $push_param // 带额外参数 前端接收 做处理
     * @return array
     */
    public function pushMessageByAlias($push_alias,$push_data,$push_param){
        $result = $this->client->push()
            ->setPlatform("all") // 推送设备，all=>所有用户，
            ->addAlias($push_alias) // 按别名进行推送 选其一
            ->addAndroidNotification($push_data['content'], $push_data['title'], 1,$push_param)// 安卓
            ->iosNotification(['sound' => '']) // 设置提示声音 空为默认声音
            ->addIosNotification($push_data['content'], $push_data['title'], '+1', true, 'iOS category', $push_param)// ios
            ->send();
        return $result;
    }

}