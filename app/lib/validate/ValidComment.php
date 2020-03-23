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


class ValidComment extends BaseValidate
{
    protected $rule = [
        'content'=>'require|isNotEmpty',
        'article'=>'require|isNotEmpty|isPositiveInteger',
        'type'=>'require|isNotEmpty|isPositiveInteger'
    ];

    protected $message =[
        'content' => '请输入评论信息',
        'article' => '请输入评论ID号',
        'type' => '请填写评论类型'
    ];
}