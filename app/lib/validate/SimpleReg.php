<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/15
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\lib\validate;


class SimpleReg extends BaseValidate
{
    protected $rule = [
        'sex'=>'require|isNotEmpty|in:1,2',
        'mobile'=>'require|isNotEmpty|isMobile|unique:user,mobile',
        'code'=>'require|isNotEmpty|length:6',
        'password'=>'require|isNotEmpty|length:6,18|confirm:repassword',
        'repassword'=>'require|isNotEmpty|confirm:password',
        'clause'=>'require|isNotEmpty|in:1,2'
    ];

    protected $message =[
        'sex'=>'请选择性别',
        'sex.in' => '性别必须是1-2之间',
        'mobile.require'=>'请输入手机号',
        'mobile.isMobile'=>'请输入正确的手机号',
        'mobile.unique'=>'手机号已存在',
        'code'=>'验证码格式为数字',
        'code.length'=>'验证码格式为6位的数字',
        'password.require'=>'密码必填',
        'password.length'=>'密码应在6-18之间',
        'password.confirm'=>'两次登录密码不一致',
        'clause.require'=>'请同意协议',
        'clause.in'=>'2未同意 1同意'
    ];
}