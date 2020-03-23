<?php
// +----------------------------------------------------------------------
// | api [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2019 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/3/21
// +----------------------------------------------------------------------
// | Author: iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\lib\validate;


class WxTokenCheck extends BaseValidate
{
    protected $rule = [
        'code' =>'require|isNotEmpty'
    ];

    protected $message = [
        'code' => '请传入微信小程序code码'
    ];
}