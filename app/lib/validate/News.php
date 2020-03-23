<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/9
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\lib\validate;


class News extends BaseValidate
{
    protected $rule = [
        'title'=>'require|isNotEmpty',
        'head_img'=>'require|isNotEmpty',
    ];

    protected $message =[
        'title' => '请填写新闻标题',
        'head_img' => '请上传文章封面图',
    ];
}