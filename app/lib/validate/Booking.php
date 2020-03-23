<?php
// +----------------------------------------------------------------------
// | sxr.ijiandian.com [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/25
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\lib\validate;


class Booking extends BaseValidate
{
    protected $rule = [
        'merchant' => 'require|isNotEmpty|number',
        'merchant_user' => 'require|isNotEmpty|number',
        'action' => 'require|isNotEmpty|number',
        'name' => 'require|isNotEmpty',
        'mobile' => 'require|isNotEmpty|isMobile',
    ];

    protected $message = [
        'merchant' =>'请输入商家ID号',
        'merchant_user' =>'请输入商家用户ID号',
        'action' =>'请输入活动ID号',
        'name' =>'请输入您的姓名',
        'mobile' =>'请输入您的手机号',
    ];
}