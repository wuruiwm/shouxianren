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


class ValidAmount extends BaseValidate
{
    protected $rule = [
        'amount'=>'require|isNotEmpty|number',
    ];

    protected $message =[
        'amount' => '请正确输入充值数目',
    ];

}