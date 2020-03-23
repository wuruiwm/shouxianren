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


class ValidMobile extends BaseValidate
{
    protected $rule = [
        'mobile'=>'require|isNotEmpty|isMobile',
    ];

    protected $message =[
        'mobile.require' => '手机号不能为空',
        'mobile.isMobile' => '手机号格式错误',
    ];
}