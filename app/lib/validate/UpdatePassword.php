<?php
// +----------------------------------------------------------------------
// | shouxianren_app [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/21
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\lib\validate;


class UpdatePassword extends BaseValidate
{
    protected $rule = [
        'old_password' => 'require|isNotEmpty',
        'password'=>'require|isNotEmpty|length:6,18|confirm:repassword',
        'repassword'=>'require|isNotEmpty|confirm:password',
    ];

    protected $message =[
        'old_password' => '请输入旧密码',
        'password'=>'请输入新密码',
        'password.length'=>'密码应在6-18之间',
        'password.confirm'=>'确认密码与新密码不一致',
    ];
}