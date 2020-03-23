<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/16
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\lib\validate;


class ValidLike extends BaseValidate
{
    protected $rule = [
        'article'=>'require|isNotEmpty|isPositiveInteger',
        'type'=>'require|isNotEmpty|isPositiveInteger'
    ];

    protected $message =[
        'article' => '请输入点赞ID号',
        'type' => '请输入点赞类型'
    ];
}