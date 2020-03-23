<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2019 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/12
// +----------------------------------------------------------------------
// | Author: iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\lib\validate;


class Qiniu extends BaseValidate
{
    protected $rule = [
        'accesskey'=>'require|isNotEmpty',
        'secretkey'=>'require|isNotEmpty',
        'storage_name'=>'require|isNotEmpty',
    ];

    protected $message =[
        'accesskey' => '请填写AccessKey值',
        'secretkey'=>'请填写SecretKey值',
        'storage_name'=>'请填写存储名称',
    ];
}