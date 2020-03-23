<?php
// +----------------------------------------------------------------------
// | sxr.ijiandian.com [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/27
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\lib\validate;


class ReceiveCoupon extends BaseValidate
{
    protected $rule = [
        'coupon_id'=>'require|isNotEmpty|isPositiveInteger',
        'type'=>'require|isNotEmpty|in:1,2,3',
    ];

    protected $message =[
        'coupon_id' => '优惠券参数不能为空',
        'type.require'=>'类型参数不能为空',
        'type.in'=>'类型参数取值范围1,2,3',
    ];
}