<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/17
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\lib\validate;


class Rechargeconfig extends BaseValidate
{
    protected $rule = [
        'integral'=>'require|isNotEmpty|between:0,100',
        'balance'=>'require|isNotEmpty|between:0,100'
    ];

    protected $message =[
        'integral' => '请正确输入积分比',
        'balance' => '请正确输入余额比'
    ];
}