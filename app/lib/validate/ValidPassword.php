<?php
// +----------------------------------------------------------------------
// | tplay [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2019 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/3/24
// +----------------------------------------------------------------------
// | Author: iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\lib\validate;


class ValidPassword extends BaseValidate
{
    protected $rule = [
        'password'=>'require|isNotEmpty',
    ];

    protected $message =[
        'password' => '请输入密码',
    ];
}