<?php
/*
 * @Author: 傍晚升起的太阳
 * @QQ: 1250201168
 * @Email: wuruiwm@qq.com
 * @Date: 2019-11-21 14:08:32
 * @LastEditors: 傍晚升起的太阳
 * @LastEditTime: 2019-12-02 09:57:13
 */
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2019 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/19
// +----------------------------------------------------------------------
// | Author: iActing <758246061@qq.com>
// +----------------------------------------------------------------------

return [
    'default_return_type' => 'json', // 默认输出类型
    'resultset_type' => 'collecton', // 数据集返回类型
  	'is_sweepcode'=>'1',
    'md5' => [
        'user_password_salt' => 't$lErGine^rtFeg',
        'token_salt' => 'fkGrL4*f#fPyi!',
        'user_pay_password_salt'=>'7$4esReSq2*7'
    ],
    'cache' => [ // 缓存
        'token_expire' => 31536000*10, //1小时 =3600
        'type'   => 'File',
        'host'   => '127.0.0.1',
        'port'   => '6379'
    ],
    'wx'=>[ // 微信配置
        'apiKey'=>'',
        'mchId'=>'',
        'partnerKey'=>'',
        'rechargeNotifyUrl'=>'',
        'payNotifyUrl'=>'',
        'description'=>'',
        //小程序
        'appid'=>'',
        'appsecret'=>'',
        'bagNotifyUrl'=>'',
    ],
    'ali'=>[ // 阿里配置
        'partner' => '', // 合作身份者id，以2088开头的16位纯数字
        'seller' => '', //商家账号
        // 商户私钥
        'rsaPriKey' => '',
        // 支付公钥
        'rsaPubKey' => '',
        'rechargeNotifyUrl' => '',
        'payNotifyUrl'=>'',
        'bagNotifyUrl'=>'',
    ]

];
