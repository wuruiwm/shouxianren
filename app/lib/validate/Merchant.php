<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/18
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\lib\validate;


class Merchant extends BaseValidate
{
    protected $rule = [
        'title'=>'require|isNotEmpty',
        'img'=>'require|isNotEmpty',
        'integral'=>'require|between:0,100',
        'proportion'=>'require|number',
    ];

    protected $message =[
        'title' => '请输入商家名称',
        'img' => '请上传图片',
        'integral.require'=>'请输入消费返还积分百分比',
        'integral.between'=>'积分百分比应是0-100的数字',
        'proportion.require'=>'请输入积分抵扣',
        'proportion.number'=>'积分抵扣应是一个数字'
    ];
}