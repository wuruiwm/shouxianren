<?php
// +----------------------------------------------------------------------
// | api [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2019 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/3/21
// +----------------------------------------------------------------------
// | Author: iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\lib\validate;


class WebTokenCheck extends BaseValidate
{
    protected $rule = [
        'mobile'=>'require|isNotEmpty|isMobile',
        'password'=>'require|isNotEmpty',
    ];

    protected $message =[
        'mobile' => '请输入正确的手机号',
        'password' => '密码不能为空'
    ];
}