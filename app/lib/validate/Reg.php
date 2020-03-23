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


class Reg extends BaseValidate
{
    protected $rule = [
        'nickname'=>'require|isNotEmpty',
        'sex'=>'require|isNotEmpty|in:1,2',
        'birthday'=>'require|isNotEmpty|dateFormat:Y-m-d|before:2010-01-01',
        'email'=>'require|isNotEmpty|email',
        'mobile'=>'require|isNotEmpty|isMobile|unique:user,mobile',
        'code'=>'require|isNotEmpty|length:6',
        'password'=>'require|isNotEmpty|length:6,18|confirm:repassword',
        'repassword'=>'require|isNotEmpty|confirm:password',
        'clause'=>'accepted'
    ];

    protected $message =[
        'nickname' => '请输入注册账号',
        'sex'=>'请选择性别',
        'sex.in' => '性别必须是1-2之间',
        'birthday'=>'请正确选择出生年月',
        'email'=>'请输入邮箱',
        'email.email'=>'请输入正确的邮箱地址',
        'mobile.require'=>'请输入手机号',
        'mobile.isMobile'=>'请输入正确的手机号',
        'mobile.unique'=>'手机号已存在',
        'code'=>'验证码格式为数字',
        'code.length'=>'验证码格式为6位的数字',
        'password'=>'密码必填',
        'password.length'=>'密码应在6-18之间',
        'password.confirm'=>'确认密码与密码内容不一致',
        'clause.accepted'=>'请同意协议'
    ];
}