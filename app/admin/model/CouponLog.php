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

namespace app\admin\model;


class CouponLog extends BaseModel
{
    protected $hidden = ['update_time','delete_time'];
    public function getUseTimeAttr($value)
    {
        if($value){
            return timeToStr(GetMkTime($value), 'Y-m-d H:i:s');
        }
        return $value;
    }
//    public function getUserAttr($value)
//    {
//        return User::where('id',$value)->field(['avatar', 'nickname','mobile'],false)->find();
//    }
}