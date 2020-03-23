<?php
// +----------------------------------------------------------------------
// | sxr.ijiandian.com [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/24
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\lib\validate;


class ValidPage extends BaseValidate
{
    protected $rule = [
        'page'=>'require|isNotEmpty|number',
        'limit'=>'require|isNotEmpty|number',
    ];

    protected $message =[
        'page.require' => 'page参数不能为空',
        'page.number' => 'page格式错误',
        'limit.require' => 'limit参数不能为空',
        'limit.number' => 'limit格式错误',
    ];
}