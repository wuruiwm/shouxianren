<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2019 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/9
// +----------------------------------------------------------------------
// | Author: iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\api\model;


use app\lib\exception\SuccessMessage;

class NewsCate extends BaseModel
{
   public static function readNewsCate()
    {

        $result = self::order('sort desc')
            ->field(['sort','status','create_time','update_time','delete_time'],true)
            ->where('status',0)
            ->select();
        throw new SuccessMessage([
            'data'=>$result
        ]);
    }
}