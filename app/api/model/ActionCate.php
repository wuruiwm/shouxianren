<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/23
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\api\model;


class ActionCate extends BaseModel
{
    public static function readActionCate()
    {
        $result = self::order('sort desc')
            ->field(['sort','status','create_time','update_time','delete_time'],true)
            ->where('status',0)
            ->where('id','>',2)
            ->select();
        return $result;
    }
}