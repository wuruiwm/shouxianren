<?php
// +----------------------------------------------------------------------
// | sxr.ijiandian.com [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2019 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/26
// +----------------------------------------------------------------------
// | Author: iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\lib\validate;


class CreateCoupon extends BaseValidate
{
    protected $rule = [
        'merchant' => 'require|isNotEmpty|number',
        'title' => 'require|isNotEmpty',
        'limit_number' => 'require|number',
        'use_condition' => 'require|number',
        'sort' => 'require|number',
        'number' => 'require|isNotEmpty|number',
        'face_value'=>'require|isNotEmpty|number'


    ];

    protected $message = [
        'merchant' =>'请选择商家号',
        'title' =>'请输入优惠券名称',
        'limit_number' =>'请正确输入每人限领张数',
        'use_condition' =>'请正确输入使用条件',
        'sort' =>'排序格式错误',
        'number' =>'优惠券数量格式错误',
        'face_value'=>'请输入优惠券面值'
    ];
}