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


class ValidRegisterType extends BaseValidate
{
    protected $rule = [
        'action'=>'require|isNotEmpty|is_in_array_exists',
    ];

    protected $message =[
        'action' => '身份类型错误',
    ];
}