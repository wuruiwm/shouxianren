<?php
// +----------------------------------------------------------------------
// | sxr.ijiandian.com [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/30
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\lib\validate;


class ValidPayPassword extends BaseValidate
{
    protected $rule = [
        'mobile'=>'require|isNotEmpty|isMobile',
        'code'=>'require|isNotEmpty|length:6',
        'password'=>'require|isNotEmpty|number|length:6|confirm:repassword',
        'repassword'=>'require|isNotEmpty|confirm:password',
    ];

    protected $message = [
        'mobile.require'=>'请输入登录账号',
        'mobile.isMobile'=>'手机号格式错误',
        'code'=>'验证码不能为空',
        'code.length'=>'验证码格式为6位的数字',
        'password.require'=>'新密码必填',
        'password.number'=>'支付密码必须数字',
        'password.length'=>'支付密码为6位纯数字',
        'password.confirm'=>'两次密码不一致',
    ];
}