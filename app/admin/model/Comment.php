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

namespace app\admin\model;


class Comment extends BaseModel
{
    public function getUserAttr($value)
    {
        $result = User::where('id',$value)->field(['avatar','nickname','mobile'],false)->find();
        return $result;
    }


    public static function deleteByIds($ids=null){
        return self::destroy($ids);
    }
}